import React from "react";
import '../App.css'; 

// class component for orders
class Orders extends React.Component {
    render() {
        return (
            <div>
                <h2>Orders</h2>
                <div className="order-container mt-5">
                    <table className="table mt-4">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>City</th>
                                <th>Province</th>
                                <th>Postal Code</th>
                                <th>Comb</th>
                                <th>Mirror</th>
                                <th>Keychain</th>
                                <th>Subtotal ($)</th>
                                <th>Tax ($)</th>
                                <th>Total ($)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {/* Loop orders and render each row */}
                        </tbody>
                    </table>
                </div>
            </div>
        );
    }
}

export default Orders;