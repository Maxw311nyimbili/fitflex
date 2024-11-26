# FitFlex - Your Fitness Journey, Simplified

Welcome to **FitFlex**, the ultimate platform designed to enhance both gym owner management and trainee fitness experiences. Whether you own a gym or are a trainee looking to track your progress, **FitFlex** brings everything you need into one easy-to-use system.

### What is FitFlex?

FitFlex goes beyond being just a fitness app—it's a community. It's built to help gym owners streamline their operations while giving trainees or gym enthusiasts the tools they need to achieve their fitness goals. With **FitFlex**, you'll get a seamless interface to manage your gym, monitor member progress, and stay connected with your fitness community.

---

### Key Features

- **Gym Owner Dashboard**: Manage gym details, monitor member activities, and handle day-to-day operations.
- **Trainee Dashboard**: Track your progress, set goals, and stay connected with the fitness community.
- **Role-based Access**: Different access levels for gym owners and trainees, ensuring relevant features for each user.
- **Fitness Journey**: Trainees can log their workouts, view progress charts, and interact with trainers and other gym members.
- **Profile Image Upload**: Trainees and gym owners can easily upload profile images for a more personalized experience.

---

### Getting Started

To get started with **FitFlex** locally, follow these steps:

#### Prerequisites

Before setting up, make sure you have the following installed on your machine:

- **PHP** (For handling backend functionality)
- **MySQL** (Database management)
- **XAMPP** (or any local server stack of your choice)
- **Visual Studio Code** (Recommended editor for a smooth development experience)

#### Setup Instructions

1. Clone or download the repository:
    ```bash
    git clone https://github.com/maxw311nyimbili/fitflex.git
    ```

2. If you're using **XAMPP**, start the Apache and MySQL services.

3. Import the database schema `fitflex.sql` into your MySQL database. You can do this through phpMyAdmin or MySQL CLI.

4. In the project directory, navigate to `includes/db_connection.php` and update the database connection with your local setup:
    ```php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "fitflex";
    ```

5. Once your server is up and running, visit `http://localhost/fitflex` in your browser, and you should see the **FitFlex** homepage!

---

### How to Use

1. **Sign Up**: Create a new account and choose your gym. Your gym selection will be reflected on your dashboard for easy access.
2. **Dashboard**: Gym owners have access to a management dashboard where they can see member data and operations, while trainees can track their personal fitness data.
3. **Image Upload**: Upload your profile image easily to personalize your account.
4. **Community Engagement**: Gym members can participate in forums, ask trainers questions, and track their progress together.

---

### Contributing

We welcome contributions! Whether you’re fixing a bug, suggesting new features, or improving documentation, your input will help make **FitFlex** even better. To contribute:

1. Fork the repository.
2. Create a feature branch (`git checkout -b feature-name`).
3. Commit your changes (`git commit -am 'Add new feature'`).
4. Push your branch (`git push origin feature-name`).
5. Open a pull request.

---

Feel free to reach out if you have any questions or need help getting started. **FitFlex** is here to support your fitness journey, no matter where you are!
