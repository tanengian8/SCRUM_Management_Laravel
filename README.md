# SCRUM Management Backend

Welcome to the backend of the SCRUM Management web application, developed as a final year project. This backend serves as the foundation for managing the logics in web-based project management.

## Design Pattern: Service-Repository

The backend implements the Service-Repository pattern, aiming to separate business logic from database operations. While the pattern is adopted, it's still a work in progress towards full utilization.

## Basic Security Mechanisms

The backend incorporates a few fundamental security measures:

1. **JWT Token Authorization Checking**: Ensures that only authorized users can access protected resources by validating JSON Web Tokens (JWT) through middleware.
   
2. **CSRF Token Checking**: Protects against Cross-Site Request Forgery (CSRF) attacks by validating CSRF tokens. Users can specify their frontend URL in the `SANCTUM_STATEFUL_DOMAINS` environment variable to exempt it from CSRF token authentication.

3. **Cross-Origin Resource Sharing (CORS)**: Allows only specific domain to access the backend of the web application, enhancing security by restricting unauthorized access.


## Frontend Repository

For the frontend component of the SCRUM Management web application, please visit the following repository:

[SCRUM Management Vue](https://github.com/tanengian8/SCRUM_Management_Vue/tree/main)

