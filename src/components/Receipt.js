import React from "react";
import '../App.css';

// class component for receipt
class Receipt extends React.Component {
    render() {
        return (
            <div>

                <header>
                </header>
                <br />

                {/* Display additional receipt details */}
                {/* Receipt section */}
                <div id="receipt-section">
                    <h2 className="success-message">Order Placed Successfully...</h2>
                    <div className="form-buy-container">
                        <div className="col-75">
                            <div className="row">
                                <div className="col-25">
                                    <h2 className="middle"><u>Receipt</u></h2>
                                    <br />

                                    {/* Render receipt data */}
                                    <p id="printreceipt"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
                <br />

                <footer>
                </footer>
            </div>
        );
    }
}

export default Receipt;
