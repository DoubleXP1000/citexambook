from flask import Flask, request, jsonify, redirect, url_for, render_template
from flask_sqlalchemy import SQLAlchemy
from flask_login import LoginManager, current_user
import logging
import os
from flask_migrate import Migrate
from dotenv import load_dotenv

db = SQLAlchemy()
login_manager = LoginManager()
migrate = Migrate()

def create_app():
    app = Flask(__name__)

    app.secret_key = os.getenv("SECRET_KEY", "supersecretkey")

    load_dotenv()

    app.config['MYSQL_HOST'] = os.getenv('MYSQL_HOST')
    app.config['MYSQL_USER'] = os.getenv('MYSQL_USER')
    app.config['MYSQL_PASSWORD'] = os.getenv('MYSQL_PASSWORD')
    app.config['MYSQL_DB'] = os.getenv('MYSQL_DB')

    app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+pymysql://' + os.getenv('MYSQL_USER') + \
        ':' + os.getenv('MYSQL_PASSWORD') + '@' + os.getenv('MYSQL_HOST') + '/' + os.getenv('MYSQL_DB')

    db.init_app(app)
    login_manager.init_app(app)
    migrate.init_app(app, db)


    # USER LOADER: tells Flask-Login how to get a user by ID
    from .models import User
    
    @login_manager.user_loader
    def load_user(user_id):
        # Flask-Login passes user_id as a string
        return User.query.get(int(user_id))

    with app.app_context():
        from .models import User

        db.create_all()

        return app