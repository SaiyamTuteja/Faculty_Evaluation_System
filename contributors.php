<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contributors</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }
        .contributor {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }
        .contributor img {
            border-radius: 50%;
            margin-right: 20px;
        }
        .contributor-details {
            flex-grow: 1;
        }
        .contributor h2 {
            margin-top: 0;
        }
        .contribute-section {
            background-color: #e9f5ff;
            padding: 20px;
            margin-top: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #0366d6;
        }
        .contribute-section h2 {
            margin-top: 0;
        }
        .contribute-section a {
            color: #0366d6;
            text-decoration: none;
        }
        .contribute-section a:hover {
            text-decoration: underline;
        }
        .contribute-section ul {
            padding-left: 20px;
        }
        .contribute-section li {
            margin-bottom: 10px;
        }
        .thank-you {
            background-color: #d4edda;
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
            border-left: 5px solid #28a745;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .thank-you h2 {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Contributors</h1>
        <?php
        $contributors = [
            [
                "name" => "Saiyam Tuteja",
                "username" => "SaiyamTuteja",
                "profile_url" => "https://github.com/SaiyamTuteja",
                "avatar_url" => "https://avatars.githubusercontent.com/u/57866007?v=4"
            ],
            [
                "name" => "Aindree Chatterjee",
                "username" => "aindree-2005",
                "profile_url" => "https://github.com/aindree-2005",
                "avatar_url" => "https://avatars.githubusercontent.com/u/86460166?v=4"
            ],
            [
                "name" => "Akarshraj  M",
                "username" => "akarshraj96",
                "profile_url" => "https://github.com/akarshraj96",
                "avatar_url" => "https://avatars.githubusercontent.com/u/112420786?v=4"
            ],
            [
                "name" => "Aniket Chakraborty",
                "username" => "Aniket-coder-711",
                "profile_url" => "https://github.com/Aniket-coder-711",
                "avatar_url" => "https://avatars.githubusercontent.com/u/129522869?v=4"
            ],
            [
                "name" => "Harshit Gupta",
                "username" => "Harshit140",
                "profile_url" => "https://github.com/Harshit140",
                "avatar_url" => "https://avatars.githubusercontent.com/u/114706608?v=4"
            ],
            [
                "name" => "Khushi Tyagi",
                "username" => "khushi463-tyagi",
                "profile_url" => "https://github.com/khushi463-tyagi",
                "avatar_url" => "https://avatars.githubusercontent.com/u/110461785?v=4"
            ],
            [
                "name" => "Mahek",
                "username" => "mahek0620",
                "profile_url" => "https://github.com/mahek0620",
                "avatar_url" => "https://avatars.githubusercontent.com/u/90625485?v=4"
            ],
            [
                "name" => "Prajwal D P",
                "username" => "prajwaldp223",
                "profile_url" => "https://github.com/prajwaldp223",
                "avatar_url" => "https://avatars.githubusercontent.com/u/123730048?v=4"
            ],
            [
                "name" => "Rahul",
                "username" => "RahulK4102",
                "profile_url" => "https://github.com/RahulK4102",
                "avatar_url" => "https://avatars.githubusercontent.com/u/84072685?v=4"
            ],
            [
                "name" => "Sanmarg Sandeep Paranjpe",
                "username" => "sanmarg",
                "profile_url" => "https://github.com/sanmarg",
                "avatar_url" => "https://avatars.githubusercontent.com/u/97022030?v=4"
            ],
            [
                "name" => "Shivam Kumar",
                "username" => "sk-upadhyay",
                "profile_url" => "https://github.com/sk-upadhyay",
                "avatar_url" => "https://avatars.githubusercontent.com/u/60136744?v=4"
            ],
            [
                "name" => "Siddheya Kulkarni",
                "username" => "Asymtode712",
                "profile_url" => "https://github.com/Asymtode712",
                "avatar_url" => "https://avatars.githubusercontent.com/u/103847145?v=4"
            ],
            [
                "name" => "Suhani Singh Paliwal",
                "username" => "suhanipaliwal",
                "profile_url" => "https://github.com/suhanipaliwal",
                "avatar_url" => "https://avatars.githubusercontent.com/u/81972124?v=4"
            ]
        ];

        foreach ($contributors as $contributor) {
            echo '<div class="contributor">';
            echo '<img src="' . $contributor["avatar_url"] . '" alt="' . $contributor["name"] . '" width="100" height="100">';
            echo '<div class="contributor-details">';
            echo '<h2>' . $contributor["name"] . '</h2>';
            echo '<p><strong>Username:</strong> <a href="' . $contributor["profile_url"] . '" target="_blank">' . $contributor["username"] . '</a></p>';
            echo '</div>';
            echo '</div>';
        }
        ?>
        <div class="thank-you">
            <h2>Thank You, Contributors!</h2>
            <p>We extend our heartfelt gratitude to all the contributors who have helped make this project a success. Your time, effort, and expertise are deeply appreciated. Together, we are building something amazing!</p>
        </div>
        <div class="contribute-section">
            <h2>Want to Contribute?</h2>
            <p>We welcome contributions from everyone. If you would like to contribute to this project, please visit our <a href="https://github.com/SaiyamTuteja/Faculty_Evaluation_System" target="_blank">GitHub repository</a>.</p>
            <p>Please read our <a href="https://github.com/SaiyamTuteja/Faculty_Evaluation_System/blob/main/CONTRIBUTING.md" target="_blank">contribution guidelines</a> before you start.</p>
            <p>Here are some ways you can contribute:</p>
            <ul>
                <li>Report bugs and suggest features via the <a href="https://github.com/SaiyamTuteja/Faculty_Evaluation_System/issues" target="_blank">issue tracker</a>.</li>
                <li>Submit pull requests to fix bugs or add new features.</li>
                <li>Improve documentation and help others understand the project better.</li>
            </ul>
        </div>
    </div>
</body>
</html>
