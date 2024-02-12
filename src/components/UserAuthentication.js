import React, { Component } from 'react';

// class component for user authentication
class UserAuthentication extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoggedIn: false,
        };
    }

    // Function to check if user is logged in
    componentDidMount() {
        // Simulate authentication status check
        const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
        this.setState({ isLoggedIn });
    }

    // Function to handle logout
    handleLogout = () => {
        const confirmed = window.confirm('Are you sure you want to logout?');
        if (confirmed) {
            // Handle logout logic here
            localStorage.setItem('isLoggedIn', 'false');
            this.setState({ isLoggedIn: false });
        }
    };

    render() {
        const { isLoggedIn } = this.state;

        return (
            <div>
                {isLoggedIn ? (
                    <button onClick={this.handleLogout}>Logout</button>
                ) : (
                    <a href="/login">Login</a>
                )}
            </div>
        );
    }
}

export default UserAuthentication;
