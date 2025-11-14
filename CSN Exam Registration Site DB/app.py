from flask import Flask, request, jsonify, redirect, url_for, render_template, flash
from werkzeug.security import generate_password_hash, check_password_hash
from project.models import User  
from flask_login import login_user
from project import create_app, db

app = create_app()

@app.route("/", methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        identifier = request.form['email']  # email or student ID
        password = request.form['password']

        # Determine if identifier is a student ID (digits) or email
        if identifier.isdigit():
            user = User.query.filter_by(student_id=int(identifier)).first()
        else:
            user = User.query.filter_by(email=identifier).first()

        if user:
            if check_password_hash(user.password, password):
                login_user(user)
                flash("Logged in successfully!", "success")
                return redirect(url_for('exam'))
            else:
                flash("Incorrect password", "danger")
        else:
            flash("User not found", "danger")

        return redirect(url_for('login'))
    
    return render_template('login2.html')




@app.route("/ExamRegistration", methods=['GET'])
def exam():
    return render_template('register_exam.html')

@app.route("/ExamSchedule", methods=['GET'])
def schedule():
    return render_template('reschedule_exam.html')




@app.route("/SignUp", methods=['GET', 'POST'])
def signup():
    if request.method == 'POST':
        first_name = request.form['first_name']
        last_name = request.form['last_name']
        email = request.form['email']
        password = request.form['password']
        confirm_password = request.form['cpassword']
        student_id = request.form['student_id']
        course = request.form['course']

        # Password confirmation check
        if password != confirm_password:
            flash("Passwords do not match", "danger")
            return redirect(url_for('signup'))

        # Hash the password before storing
        hashed_password = generate_password_hash(password)

        # Check if email or student_id already exists
        existing_user = User.query.filter((User.email==email) | (User.student_id==student_id)).first()
        if existing_user:
            flash("User with this email or Student ID already exists", "danger")
            return redirect(url_for('signup'))
        
        # Create new user object
        new_user = User(
            first_name=first_name,
            last_name=last_name,
            email=email,
            password=hashed_password,
            cpassword=hashed_password,  # optional, just to match your model
            student_id=student_id,
            course=course
        )

        # Add and commit to DB
        db.session.add(new_user)
        db.session.commit()

        flash("Account created successfully!", "success")
        return redirect(url_for('login'))

    return render_template('new_signup_page2.html')

if __name__ == "__main__":
    app.run(debug=True)
