import React from "react";

// class component for CartPage
class CartPage extends React.Component {
    constructor(props) {
        super(props);
    }

    // Function to calculate the total bill
    calculateQuantity = (cart) => {
        return cart.reduce((total, product) => total + product.quantity, 0);
    };
    // Function to calculate the total bill
    calculateTotal = (cart) => {
        return cart.reduce((total, product) => total + product.price * product.quantity, 0);
    };

    // Function to calculate tax
    calculateTax = (total) => {
        return total * 0.13; // 13% tax
    };

    render() {
        // Get the cart from the props
        const { cart } = this.props;
        const total = this.calculateTotal(cart);
        const tax = this.calculateTax(total);
        const totalWithTax = total + tax;
        const quantity = this.calculateQuantity(cart);

        return (
            <div className="cart-card-layout">
                <h1>Shopping Cart</h1>
                <div>
                    {/* Display selected products */}
                    {cart.map((product, index) => (
                        <div key={index} className="cart-item">
                            <img src={process.env.PUBLIC_URL + product.images[0]} alt={product.name} style={{ width: '50px', height: '50px', objectFit: 'cover' }} />
                            <span>{product.name} - ${product.price} x {product.quantity} = ${product.price * product.quantity}</span>
                            <button onClick={() => this.props.removeFromCart(index)}>Remove</button>
                        </div>
                    ))}
                </div>
                <div>
                    <h2>Total Quantity: {quantity}</h2>
                    <h2>Subtotal: ${total.toFixed(2)}</h2>
                    <h2>Tax: ${tax.toFixed(2)}</h2>
                    <h2>Total: ${totalWithTax.toFixed(2)}</h2>
                    <button onClick={this.props.handleBuyNow}>Buy Now</button>
                </div>
            </div>
        );
    }
}

export default CartPage;
