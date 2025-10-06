import requests
import json
import os
import logging

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

class TradingApiReport:
    def __init__(self, username, auth_token):
        self.username = username
        self.auth_token = auth_token
        self.url = 'https://www.trade-copier.com/webservice/v4/reporting/getReporting.php'

    def get_reports(self, year):
        reports = []
        headers = {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Auth-Username': self.username,
            'Auth-Token': self.auth_token,
        }

        # Iterate through each month to get reports
        for month in range(1, 13):  # Months are 1-12
            response = requests.post(self.url, headers=headers, data={'month': month, 'year': year})
            
            # Check if the request was successful
            if response.status_code == 200:
                data = response.json()
                
                if 'reporting' in data:
                    for report in data['reporting']:
                        reports.append(report)
                else:
                    logging.error('Invalid JSON or missing reporting key')
            else:
                logging.error(f"HTTP Error: {response.status_code} - {response.text}")

        self.save_reports_to_json(reports)
        return reports

    def save_reports_to_json(self, reports, filename='leaderboard.json'):
        storage_path = 'storage/app'
        os.makedirs(storage_path, exist_ok=True)
        file_path = os.path.join(storage_path, filename)

        try:
            with open(file_path, 'w') as json_file:
                json.dump(reports, json_file, indent=4)
            logging.info(f"Reports data saved to {file_path}")
        except Exception as e:
            logging.error(f"Failed to save reports data: {e}")

username = 'jamespereiraofficial'
auth_token = 'jM3NTc1N2YxNDFmODIwZjQ3NTg3OWR'
year = 2024

trading_api_report = TradingApiReport(username, auth_token)
reports = trading_api_report.get_reports(year)

#print("Reports:", reports)
