# TreeAppWeb

A modern web platform for teachers and students to manage assignments, submissions, feedback, and more.

## Features

- **Role-based Dashboards**: Separate dashboards for teachers and students.
- **Assignment Management**: Teachers can create tasks, upload files, and provide feedback.
- **Student Submissions**: Students can submit, edit, and download their work.
- **Course Management**: Browse and manage courses and related materials.
- **Calendar**: View schedules and upcoming deadlines.
- **Profile Management**: Update user information and settings.
- **Dark Mode**: Toggle for comfortable viewing.
- **Secure Authentication**: Login and logout functionality.
- **Responsive Design**: Mobile-friendly with Bootstrap 5.

## Getting Started

### Prerequisites

- PHP 7.4 or higher
- MySQL/MariaDB
- Web server (e.g., Apache, XAMPP)
- Composer (optional, if you add dependencies)

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/BonganiMlotshwa/TreeAppWeb.git
   cd TreeAppWeb
   ```

2. **Set up the database:**
   - Import the `tree_app.sql` file into your MySQL server.
   - Update database credentials in `db.php` if needed.

3. **Configure your web server:**
   - Place the project folder in your web server's root directory (e.g., `htdocs` for XAMPP).
   - Ensure PHP and MySQL are running.

4. **Assets:**
   - All required assets (CSS, JS, fonts, images) are included in the `assets/` directory.

### Usage

- Visit `http://localhost/TreeAppWeb` in your browser.
- Sign up as a new user or log in with existing credentials.
- Teachers and students will see different dashboards and features based on their roles.

### File Structure

```
TreeAppWeb/
  â”œâ”€â”€ assets/                # CSS, JS, images, fonts, and data files
  â”œâ”€â”€ auth.php               # Authentication logic
  â”œâ”€â”€ db.php                 # Database connection
  â”œâ”€â”€ index.php              # Main dashboard (role-based)
  â”œâ”€â”€ login.php              # Login page
  â”œâ”€â”€ logout.php             # Logout logic
  â”œâ”€â”€ signup.php             # Registration page
  â”œâ”€â”€ teacher_dashboard.php  # Teacher dashboard
  â”œâ”€â”€ student_dashboard.php  # Student dashboard
  â”œâ”€â”€ courses.php            # Course management
  â”œâ”€â”€ calendar.php           # Calendar view
  â”œâ”€â”€ profile.php            # User profile
  â”œâ”€â”€ tasks.php              # Task management
  â”œâ”€â”€ submit_task.php        # Task submission
  â”œâ”€â”€ view_submissions.php   # View student submissions
  â”œâ”€â”€ view_task.php          # View individual task
  â”œâ”€â”€ report_problem.php     # Report issues
  â”œâ”€â”€ view_reports.php       # View reported problems
  â”œâ”€â”€ uploads/               # Uploaded files
  â””â”€â”€ tree_app.sql           # Database schema
```

### Customization

- **Dark Mode**: Edit `assets/darkmode.css` and `assets/darkmode.js` for appearance tweaks.
- **Branding**: Replace images in `assets/` (e.g., logos) as needed.
- **Database**: Adjust `tree_app.sql` and `db.php` for your environment.

### Security Notes

- Always sanitize user input.
- Use HTTPS in production.
- Change default database credentials.

### License

[MIT](LICENSE) (or your chosen license)

---

## Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change.

---

## Contact

For questions or support, please contact [bmangalisomlotshwa@gmail.com].

---

## Changelog

### [Unreleased]
- Teachers can now set a due date when creating assignments/tasks.
- The calendar view highlights assignment due dates for both teachers and students.
- Clicking a highlighted due date in the calendar opens the task details page. 

