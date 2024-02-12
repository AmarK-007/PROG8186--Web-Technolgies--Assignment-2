import React, { Component } from 'react';

// class component for AccountPage
class AccountPage extends Component {
    constructor(props) {
        super(props);
        this.state = {
            name: '',
            email: '',
            address: '',
            phone: '',
            username: '',
            password: ''
        };
    }

    handleSubmit = (e) => {
        e.preventDefault();

        // Logic to save user credentials
        const newUser = {
            name: this.state.name,
            email: this.state.email,
            address: this.state.address,
            phone: this.state.phone,
            username: this.state.username,
            password: this.state.password
        };

        // Saved user credentials to localStorage as an array of objects
        const existingUsers = JSON.parse(localStorage.getItem('users')) || [];
        const updatedUsers = [...existingUsers, newUser];
        localStorage.setItem('users', JSON.stringify(updatedUsers));

        // Log the updatedUsers array
        //console.log('Stored users:', updatedUsers);

        // Redirect to login page after creating accountwith delay the redirect by 1 second
        setTimeout(() => {
            window.location.href = '/login';
        }, 1000);
    };

    render() {
        return (
            <div className="container mt-5">
                <div className="row justify-content-center">
                    <div className="col-md-6">
                        <div className="card">
                            <div className="card-body">
                                <h2 className="card-title text-center mb-4">Create Account</h2>
                                <form onSubmit={this.handleSubmit}>
                                    <div className="mb-3">
                                        <label htmlFor="name" className="form-label">Name:</label>
                                        <input type="text" id="name" name="name" value={this.state.name} onChange={(e) => this.setState({ name: e.target.value })} className="form-control" />
                                    </div>
                                    <div className="mb-3">
                                        <label htmlFor="email" className="form-label">Email:</label>
                                        <input type="email" id="email" name="email" value={this.state.email} onChange={(e) => this.setState({ email: e.target.value })} className="form-control" />
                                    </div>
                                    <div className="mb-3">
                                        <label htmlFor="address" className="form-label">Address:</label>
                                        <input type="text" id="address" name="address" value={this.state.address} onChange={(e) => this.setState({ address: e.target.value })} className="form-control" />
                                    </div>
                                    <div className="mb-3">
                                        <label htmlFor="phone" className="form-label">Phone:</label>
                                        <input type="tel" id="phone" name="phone" value={this.state.phone} onChange={(e) => this.setState({ phone: e.target.value })} className="form-control" />
                                    </div>
                                    <div className="mb-3">
                                        <label htmlFor="username" className="form-label">Username:</label>
                                        <input type="text" id="username" name="username" value={this.state.username} onChange={(e) => this.setState({ username: e.target.value })} className="form-control" />
                                    </div>
                                    <div className="mb-3">
                                        <label htmlFor="password" className="form-label">Password:</label>
                                        <input type="password" id="password" name="password" value={this.state.password} onChange={(e) => this.setState({ password: e.target.value })} className="form-control" />
                                    </div>
                                    <button type="submit" className="btn btn-primary">Create Account</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default AccountPage;
