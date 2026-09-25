document.addEventListener('DOMContentLoaded', function () {
    const loginRegisterLink = document.getElementById('login-register-link');
    const popup = document.getElementById('popup');
    const userWelcome = document.getElementById('user-info')?.querySelector('.welcome-message');

    // Check if the user is already signed in dynamically
    const isUserSignedIn = checkUserSignInStatus();
    const username = getUsername(); // Retrieve username from your authentication logic

    if (isUserSignedIn) {
        showUsername();
    } else {
        closePopup();
    }

    // Add event listener to the login/register link
    if (loginRegisterLink) {
        loginRegisterLink.addEventListener('click', function (event) {
            event.preventDefault();
            showPopup();
        });
    }

    // Add event listener to the sign-in form
    const signinForm = document.getElementById('signin-form');
    if (signinForm) {
        signinForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            const usernameOrEmail = document.getElementById('signin-username-email').value;
            const password = document.getElementById('signin-password').value;

            try {
                const response = await authenticateUser(usernameOrEmail, password);

                if (response.success) {
                    setUsername(response.username);
                    showUsername();
                } else {
                    alert('Login failed. Please check your credentials.');
                }
            } catch (error) {
                alert('An error occurred during authentication.');
            }
        });
    }

    // Add event listener to the register form
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            registerUser();
        });
    } else {
        console.error('Register form not found.');
    }

    // Function to show the registration or sign-in form
    function showForm(formId) {
        const signInForm = document.getElementById('signin-form');
        const signUpForm = document.getElementById('register-form');

        if (formId === 'signin-form') {
            signInForm.style.display = 'block';
            signUpForm.style.display = 'none';
        } else if (formId === 'register-form') {
            signInForm.style.display = 'none';
            signUpForm.style.display = 'block';
        }

        if (popup) {
            popup.style.display = 'block';
        }
        if (userWelcome) {
            userWelcome.style.display = 'none';
        }
    }

    // Function to show the welcome message with the username
    function showUsername() {
        if (popup) {
            popup.style.display = 'none';
        }
        if (userWelcome) {
            userWelcome.style.display = 'block';
            const welcomeMessage = document.getElementById('welcome-message');
            if (welcomeMessage) {
                welcomeMessage.innerHTML = `Hi, ${username}!`;
            }
        }
    }

    // Function to show the popup
    function showPopup() {
        if (popup) {
            popup.style.display = 'block';
        }
    }

    // Function to close the popup
    function closePopup() {
        if (popup) {
            popup.style.display = 'none';
        }
    }

    // Replace these functions with your actual authentication logic
    function checkUserSignInStatus() {
        // Example: Check if a session token exists
        return sessionStorage.getItem('sessionToken') !== null;
    }

    function getUsername() {
        // Example: Retrieve username from your session or authentication logic
        return sessionStorage.getItem('username') || 'Guest';
    }

    function setUsername(username) {
        // Example: Set username in session storage
        sessionStorage.setItem('username', username);
    }

    async function authenticateUser(usernameOrEmail, password) {
        // Example: Make an AJAX request to authenticate the user on the server
        const response = await axios.post('./handling/authenticate.php', {
            usernameOrEmail,
            password
        });

        return response.data;
    }

    async function registerUser() {
        const username = document.getElementById('register-username').value;
        const firstName = document.getElementById('register-first-name').value;
        const lastName = document.getElementById('register-last-name').value;
        const email = document.getElementById('register-email').value;
        const password = document.getElementById('register-password').value;

        try {
            const response = await axios.post('./handling/register.php', {
                username,
                firstName,
                lastName,
                email,
                password
            });

            if (response.data.success) {
                setUsername(response.data.username);
                showUsername();
            } else {
                alert(response.data.error || 'Registration failed. Please try again.');
            }
        } catch (error) {
            console.error('An error occurred during registration:', error);
            alert('An error occurred during registration.');
        }
    }
});
