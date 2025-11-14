from . import db
from flask_login import UserMixin

class User(UserMixin, db.Model):
    student_id = db.Column(db.String(20), primary_key=True)
    first_name = db.Column(db.String(100), nullable=False)
    last_name = db.Column(db.String(100), nullable=False)
    email = db.Column(db.String(100), unique=True, nullable=False)
    password = db.Column(db.String(1000), nullable=False)
    cpassword = db.Column(db.String(1000), nullable=False)
    course = db.Column(db.String(100), nullable=True)
    created_at = db.Column(db.DateTime, server_default=db.func.now())

    # Flask-Login requires a method to get a unique ID
    def get_id(self):
        return str(self.student_id)  # must return a string

class Exam(db.Model):
    exam_id = db.Column(db.String(20), primary_key=True)
    date = db.Column(db.Date, nullable=False)
    course = db.Column(db.String(100), nullable=False)

class Location(db.Model):
    location_id = db.Column(db.String(20), primary_key=True)
    campus = db.Column(db.String(100), nullable=False)
    room_num = db.Column(db.String(20), nullable=False)

    def __repr__(self):
        return '<User %r>' % self.username