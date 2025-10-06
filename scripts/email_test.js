import { Resend } from "resend";

const resend = new Resend("re_AkWTrjP7_NoLjhxoM1bSB6W6pFinpuS1v");

async function sendEmail() {
    try {
        const response = await resend.emails.send({
            from: "James Trading Group <james@jamestradinggroup.com>",
            to: ["codingninjaprox@gmail.com"],
            subject: "hello world",
            html: "<p>it works!</p>",
        });
        console.log("Email sent successfully:", response);
    } catch (error) {
        console.error("Error sending email:", error);
    }
}

sendEmail();
