import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import '../App.css';

// class component for logout
const Logout = () => {
    const navigate = useNavigate(); // Use useNavigate hook
    const [loggedIn, setLoggedIn] = useState(true); // State to track login status

    // Logout logic
    const logout = () => {
        // sessionStorage.removeItem('username');

        // Update login status to false
        setLoggedIn(false);

        // Redirect to home page after logout
        navigate('/'); // Redirect to the homepage
    }

    // Call logout function when component mounts
    React.useEffect(() => {
        logout();
    }, []); // Empty dependency array ensures useEffect runs only once after mount

    return (
        <div>

            <div id="receipt-section" className="text-center">
                <h2 className="success-message">You have been successfully logged out...</h2>
            </div>

            {!loggedIn && (
                <button onClick={() => navigate('/login')}>Login</button>
            )}
        </div>
    );
}

export default Logout;
