<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <style>
        /* Reset default browser styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            color: #333;
            line-height: 1.6;
        }

        header {
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        header h1 {
            margin-bottom: 10px;
            font-size: 2.5rem;
        }

        header img {
            position: absolute;
            top: 10px;
            left: 10px;
            height: 50px;
            width: auto;
        }

        nav ul {
            list-style-type: none;
        }

        nav ul li {
            display: inline;
            margin-right: 20px;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        nav ul li a.active {
            text-decoration: underline;
        }

        nav ul li a:hover {
            color: #ffdd57;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .contact-form, .contact-info {
            flex: 1;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px;
            min-width: 300px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .contact-form:hover, .contact-info:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .contact-form h2, .contact-info h2 {
            margin-bottom: 20px;
            font-size: 1.8rem;
            color: #007bff;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s, transform 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
        }

        button[type="submit"]:active {
            background-color: #00408d;
        }

        .hidden {
            display: none;
        }

        .contact-info p {
            margin-bottom: 10px;
            font-size: 1rem;
        }

        footer {
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            text-align: center;
            margin-top: 40px;
        }

        footer h3 {
            margin-bottom: 10px;
            font-size: 1.5rem;
        }

        footer ul {
            list-style-type: none;
            padding: 0;
        }

        footer ul li {
            display: inline;
            margin-right: 10px;
        }

        footer ul li a {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s;
        }

        footer ul li a:hover {
            color: #ffdd57;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                padding: 0 10px;
            }

            .contact-form, .contact-info {
                margin: 10px 0;
            }

            header h1 {
                font-size: 2rem;
            }

            footer h3 {
                font-size: 1.2rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get form elements
            const form = document.getElementById('contactForm');
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const subjectInput = document.getElementById('subject');
            const messageInput = document.getElementById('message');
            const confirmationMessage = document.getElementById('confirmationMessage');
            const errorMessages = document.querySelectorAll('.error-message');

            // Event listener for form submission
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                // Validate form inputs
                if (validateForm()) {
                    // Prepare data for submission
                    const formData = {
                        name: nameInput.value,
                        email: emailInput.value,
                        subject: subjectInput.value,
                        message: messageInput.value
                    };

                    // Simulate form submission (replace with actual backend submission)
                    console.log('Form submitted:', formData);

                    // Show confirmation message
                    confirmationMessage.classList.remove('hidden');
                    form.reset();
                }
            });

            // Function to validate form inputs
            function validateForm() {
                let isValid = true;

                // Validate name
                if (!nameInput.value.trim()) {
                    displayErrorMessage(nameInput, 'Name is required');
                    isValid = false;
                } else {
                    hideErrorMessage(nameInput);
                }

                // Validate email
                if (!emailInput.value.trim() || !isValidEmail(emailInput.value.trim())) {
                    displayErrorMessage(emailInput, 'Enter a valid email address');
                    isValid = false;
                } else {
                    hideErrorMessage(emailInput);
                }

                // Validate subject
                if (!subjectInput.value.trim()) {
                    displayErrorMessage(subjectInput, 'Subject is required');
                    isValid = false;
                } else {
                    hideErrorMessage(subjectInput);
                }

                // Validate message
                if (!messageInput.value.trim()) {
                    displayErrorMessage(messageInput, 'Message is required');
                    isValid = false;
                } else {
                    hideErrorMessage(messageInput);
                }

                return isValid;
            }

            // Function to display error message
            function displayErrorMessage(input, message) {
                const errorMessage = input.nextElementSibling;
                errorMessage.textContent = message;
                errorMessage.classList.remove('hidden');
                input.classList.add('input-error');
            }

            // Function to hide error message
            function hideErrorMessage(input) {
                const errorMessage = input.nextElementSibling;
                errorMessage.textContent = '';
                errorMessage.classList.add('hidden');
                input.classList.remove('input-error');
            }

            // Function to validate email format
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
        });
    </script>
</head>
<body>
    <header>
        <img src="C:/Users/mahek/OneDrive/Desktop/facultyEvaluation/Faculty_Evaluation_System/logo.png" alt="Logo">
        <h1>Contact Us</h1>
        <nav>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="contact.html" class="active">Contact</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <section class="contact-form">
            <h2>Get in Touch</h2>
            <form action="contact.php" method="post" id="contactForm">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                    <span class="error-message hidden"></span>
                </div>
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" required>
                    <span class="error-message hidden"></span>
                </div>
                <div class="form-group">
                    <label for="subject">Subject:</label>
                    <input type="text" id="subject" name="subject" required>
                    <span class="error-message hidden"></span>
                </div>
                <div class="form-group">
                    <label for="message">Message:</label>
                    <textarea id="message" name="message" rows="6" required></textarea>
                    <span class="error-message hidden"></span>
                </div>
                <button type="submit">Submit</button>
            </form>
            <p id="confirmationMessage" class="hidden">Thank you for contacting us. We will get back to you soon!</p>
        </section>

        <section class="contact-info">
            <h2>Contact Information</h2>
            <p><strong>Address:</strong> 1234 Street Name, City, Country</p>
            <p><strong>Phone:</strong> +123 456 7890</p>
            <p><strong>Email:</strong> info@example.com</p>
            <p><strong>Office Hours:</strong> Monday to Friday, 9am - 5pm</p>
        </section>
    </div>

    <footer>
        <h3>Follow Us</h3>
        <ul>
            <li><a href="#">Facebook</a></li>
            <li><a href="#">Twitter</a></li>
            <li><a href="#">Instagram</a></li>
            <li><a href="#">LinkedIn</a></li>
        </ul>
    </footer>
</body>
</html>

