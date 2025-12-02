from flask import Flask, request, jsonify, redirect, url_for, render_template, flash
from werkzeug.security import generate_password_hash, check_password_hash
from project.models import User  
from flask_login import login_user, logout_user
from project import create_app, db
from project.models import Exam
from flask_login import login_required, current_user

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

@app.route('/logout')
@login_required
def logout():
    logout_user()  # This clears the user's session
    flash("You have been logged out successfully.", "success")
    return redirect(url_for('login'))  # Redirect to login page

@app.route("/dashboard")
@login_required
def dashboard():
    # Fetch all exams for the logged-in student
    exams = Exam.query.filter_by(student_id=current_user.student_id).order_by(Exam.exam_date, Exam.exam_time).all()
    
    return render_template("dashboard.html", exams=exams, student=current_user)


@app.route("/ExamRegistration", methods=['GET', 'POST'])
@login_required
def exam():
    if request.method == 'POST':
        student_id = request.form['student_id']
        exam_code = request.form['exam_code']
        exam_location = request.form['exam_location']
        exam_date = request.form['exam_date']
        exam_time = request.form['exam_time']

        # --- Step 1: Check for duplicate registration ---
        existing = Exam.query.filter_by(
            student_id=student_id,
            exam_code=exam_code,
            exam_date=exam_date,
            exam_time=exam_time
        ).first()

        if existing:
            flash("You have already registered for this exam at the selected date and time.", "danger")
            return redirect(url_for('exam'))

        # --- Step 2: Create new registration ---
        reg = Exam(
            student_id=student_id,
            exam_code=exam_code,
            exam_location=exam_location,
            exam_date=exam_date,
            exam_time=exam_time
        )

        db.session.add(reg)
        db.session.commit()

        # --- Step 3: Show confirmation ---
        return render_template(
            "confirmation.html",
            student_id=student_id,
            exam_code=exam_code,
            exam_location=exam_location,
            exam_date=exam_date,
            exam_time=exam_time
        )

    return render_template('register_exam.html')


@app.route("/ExamSchedule", methods=['GET', 'POST'])
@login_required
def schedule():
    # Fetch all exams for current user
    exams = Exam.query.filter_by(student_id=current_user.student_id).order_by(Exam.exam_date, Exam.exam_time).all()

    if request.method == 'POST':
        exam_id = request.form.get('exam_id')
        new_date = request.form.get('new_date')
        new_time = request.form.get('new_time')
        action = request.form.get('action')

        if not exam_id:
            flash("No exam selected.", "danger")
            return redirect(url_for('schedule'))

        # Get exam using correct primary key
        exam_to_update = Exam.query.get(int(exam_id))

        if not exam_to_update:
            flash("Selected exam not found.", "danger")
            return redirect(url_for('schedule'))

        if action == 'cancel':
            db.session.delete(exam_to_update)
            db.session.commit()
            flash("Exam canceled successfully!", "success")
        elif action == 'reschedule':
            if new_date:
                exam_to_update.exam_date = new_date
            if new_time:
                exam_to_update.exam_time = new_time
            db.session.commit()
            flash("Exam rescheduled successfully!", "success")

        return redirect(url_for('schedule'))

    return render_template('reschedule_exam.html', exams=exams)







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
