from flask import Flask, request, jsonify, redirect, url_for, render_template
from project import create_app

app = create_app()

@app.route("/", methods=['GET'])
def home():
    return render_template('login.html')

@app.route("/ExamRegistration", methods=['GET'])
def exam():
    return render_template('register_exam.html')

@app.route("/ExamSchedule", methods=['GET'])
def schedule():
    return render_template('reschedule_exam.html')

@app.route("/SignUp", methods=['GET'])
def signup():
    return render_template('signup_page.html')

if __name__ == "__main__":
    app.run(debug=True)
