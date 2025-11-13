from . import db
from flask_login import UserMixin

class User(UserMixin, db.Model):
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(100), unique=True, nullable=False)
    email = db.Column(db.String(100), unique=True, nullable=False)
    course = db.Column(db.String(100), nullable=False)
    password_hash = db.Column(db.String(1000), nullable=False)
    created_at = db.Column(db.DateTime, server_default=db.func.now())

class Exam(db.Model):
    exam_id = db.Column(db.Integer, primary_key=True)
    date = db.Column(db.Date, nullable=False)
    course = db.Column(db.String(100), nullable=False)

class Location(db.Model):
    location_id = db.Column(db.Integer, primary_key=True)
    campus = db.Column(db.String(100), nullable=False)
    room_num = db.Column(db.String(20), nullable=False)

    def __repr__(self):
        return '<User %r>' % self.username