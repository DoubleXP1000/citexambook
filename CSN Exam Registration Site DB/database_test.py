import mysql.connector
from mysql.connector import Error

print("Starting test...")

try:
    connection = mysql.connector.connect(
        host="localhost",
        user="root",
        password="AlmostDone13~"
    )
    print("Connection successful!")

except Error as e:
    print("Error:", e)
