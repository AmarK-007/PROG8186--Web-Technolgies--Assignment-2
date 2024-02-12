import React from 'react';
import UserAuthentication from './UserAuthentication';
import { Link } from 'react-router-dom';

// class component for header
class Header extends React.Component {
    
    // Function to calculate the total quantity of products in the cart
    calculateTotalQuantity = () => {
        const { cart } = this.props;
        if (cart && cart.length > 0) {
            return cart.reduce((total, product) => total + product.quantity, 0);
        } else {
            return 0; // Return 0 if cart is undefined or empty
        }
    };

    render() {
        const totalQuantity = this.calculateTotalQuantity();

        return (
            <header>
                <h1 className="shoecart-title">Shoecart</h1>
                <div className="header-links">
                    <div>
                        <Link to="/">Home</Link>
                        {totalQuantity > 0 ? (
                            <Link to="/cart">Cart ({totalQuantity})</Link>
                        ) : (
                            <Link to="/cart">Cart</Link>
                        )}
                    </div>
                    <UserAuthentication />
                </div>
            </header>
        );
    }
}

export default Header;
