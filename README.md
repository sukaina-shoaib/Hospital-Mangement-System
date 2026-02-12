# Hospital Management System

A web-based Hospital Management System (HMS) developed to digitize hospital operations such as patient management, appointment scheduling, laboratory tests, radiology services, and treatment record maintenance.

This project was developed as part of a Software Engineering course.

## Overview

The Hospital Management System replaces manual hospital record-keeping with a centralized digital platform.

The system provides separate portals for:

* Doctors
* Patients

It improves operational efficiency, reduces paperwork, and enhances communication between healthcare providers and patients.

## Key Features

### Authentication System

* Secure login functionality
* Role-based access (Patient / Doctor)

### Appointment Management

* Book appointments
* View scheduled appointments
* Cancel appointments
* Generate token number and receipt

### Lab and Radiology Management

* Request laboratory tests
* Request CT-Scan, X-Ray, Ultrasound
* Generate lab reports
* View test history

### Doctor Panel

* View daily appointments
* Access patient medical history
* Record diagnosis
* Prescribe treatment
* Schedule follow-up visits

### Physiotherapy Module

* Suggest exercises
* Track physiotherapy sessions
* Schedule follow-ups

### Billing System

* Medical bill generation
* Test bill generation
* Prescription generation

## Technologies Used

Frontend: HTML, CSS, JavaScript
Backend: PHP
Database: MySQL
Server: Apache (XAMPP)
Version Control: Git and GitHub

## User Roles

### Patient

* Register and login
* Book appointments
* View treatments
* View laboratory reports
* Receive prescriptions

### Doctor

* Secure login
* View appointment list
* Record diagnosis
* Prescribe medicines
* Refer patients for tests or physiotherapy
* 
## Documentation

The `documentation/` folder contains the complete academic project report.

It includes:

* Executive Summary
* Background and Problem Statement
* Methodology
* System Analysis and Design
* UML Diagrams:

  * Use Case Diagrams
  * Sequence Diagrams
  * Class Diagram
  * Object Diagram
  * Database Diagram
  * Deployment Diagram
* Actor Use Case Tables
* Report Lists
* Sample Bills (Medical Bill, Test Bill, Prescription)
* Glossary and UML Symbols
* References

The documentation follows academic formatting standards and was submitted as part of coursework requirements.

## Installation Guide

### Prerequisites

* Install XAMPP
* Install Git
* Use a modern web browser

---

### Step 1: Clone Repository

```bash
git clone https://github.com/sukaina-shoaib/Hospital-Mangement-System.git
cd Hospital-Mangement-System
```

---

### Step 2: Setup Database

1. Open phpMyAdmin
2. Import the database file if provided
   or use Data.php to create tables

### Step 3: Configure Database

1. Open: Database.php
2. Update the **hostname, username, and password** as per your MySQL server settings if needed.

### Step 4: Run the Project

1. Move the project folder to:
   C:\xampp\htdocs\

2. Start Apache and MySQL from XAMPP

3. Open browser and visit:

http://localhost/Hospital-Mangement-System
or directly run login.php file

## Project Objectives

* Replace manual hospital record systems
* Improve patient-doctor communication
* Reduce administrative workload
* Provide centralized patient data management
* Ensure structured hospital workflow


## Future Enhancements

* Mobile application version
* SMS and email notifications
* Online payment integration
* Admin dashboard with analytics
* Advanced security and data encryption
* AI-based diagnostic assistance


