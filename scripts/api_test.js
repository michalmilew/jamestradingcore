const https = require('https');
const fs = require('fs');
const path = require('path');

// Simple logging functions
const logInfo = (message) => console.info(`${new Date().toISOString()} - INFO - ${message}`);
const logError = (message) => console.error(`${new Date().toISOString()} - ERROR - ${message}`);

class TradingApiReport {
  constructor(username, authToken) {
    this.username = username;
    this.authToken = authToken;
    this.url = 'https://www.trade-copier.com/webservice/v4/reporting/getReporting.php';
  }

  // Method to make HTTP POST requests without third-party libraries
  makePostRequest(url, data, headers, callback) {
    const urlObject = new URL(url);
    const options = {
      hostname: urlObject.hostname,
      path: urlObject.pathname,
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        ...headers
      }
    };

    const req = https.request(options, (res) => {
      let body = '';

      res.on('data', (chunk) => {
        body += chunk;
      });

      res.on('end', () => {
        callback(null, body, res);
      });
    });

    req.on('error', (error) => {
      callback(error);
    });

    req.write(data);
    req.end();
  }

  async getReports(year) {
    const reports = [];
    const headers = {
      'Auth-Username': this.username,
      'Auth-Token': this.authToken
    };

    for (let month = 1; month <= 12; month++) {
      try {
        const data = `month=${month}&year=${year}`;
        const response = await new Promise((resolve, reject) => {
          this.makePostRequest(this.url, data, headers, (err, body, res) => {
            if (err) return reject(err);
            resolve({ body, statusCode: res.statusCode });
          });
        });

        if (response.statusCode === 200) {
          const data = JSON.parse(response.body);

          if (data && data.reporting) {
            reports.push(...data.reporting);
          } else {
            logError('Invalid JSON or missing reporting key');
          }
        } else {
          logError(`HTTP Error: ${response.statusCode} - ${response.body}`);
        }
      } catch (error) {
        logError(`Error fetching data for month ${month}: ${error.message}`);
      }
    }

    this.saveReportsToJson(reports);
    return reports;
  }

  saveReportsToJson(reports, filename = 'leaderboard.json') {
    const storagePath = path.join(__dirname, 'storage', 'app');
    const filePath = path.join(storagePath, filename);

    fs.mkdir(storagePath, { recursive: true }, (err) => {
      if (err) {
        return logError(`Failed to create directory: ${err.message}`);
      }

      fs.writeFile(filePath, JSON.stringify(reports, null, 4), (err) => {
        if (err) {
          return logError(`Failed to save reports data: ${err.message}`);
        }
        logInfo(`Reports data saved to ${filePath}`);
      });
    });
  }
}

// Usage
const username = 'jamespereiraofficial';
const authToken = 'jM3NTc1N2YxNDFmODIwZjQ3NTg3OWR';
const year = 2024;

const tradingApiReport = new TradingApiReport(username, authToken);
tradingApiReport.getReports(year).then((reports) => {
  console.log('Reports:', reports);
}).catch(error => {
  logError(`Failed to fetch reports: ${error.message}`);
});
