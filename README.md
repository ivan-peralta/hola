Prerequisites:
- Install docker and docker-composer
- File apps/index.php is "Hello World" to check docker
- Folder apps/hola_web is hola web


Run docker:
- Inside folder (in my computer: /var/www/html/hola) run: docker-compose up 
- Check if server is active: http://localhost:5000
- Install required libraries (intl, pdo, ext-mbstring...)


Symfony:
- Download symfony 3.4 and run app inside hola_web folder (without docker): php bin/console server:start
- Run in http://localhost:8000
- T1: Create User entity with ORM (src/AppBundle/Entity/User)

- T2: Done with POSTMAN: (Api page: src/AppBundle/Controller/UserAPIController)
    - Create: POST api/1.0/create (Created two users page1 and page2 with same name, username and password, associated PAGE_1 and PAGE_2 roles)
    - Retrieve: GET api/1.0/retrieve/{id}
    - Update: PUT api/1.0/update/{id}
    - Delete: DELETE api/1.0/delete/{id}
    - Basic Auth sent in Postman: User: hola / Password: hola
    - ADMIN role for Create, Edit, Delete not needed because URL not accesible by browser. Example restrict access in security.yml:
        access_control:
        - { path: /api/path, roles: [ROLE_ADMIN] }

- T3-5: (Page/1-2: src/AppBundle/Controller/PageControler)
    - Create user login form (src/AppBundle/Security/LoginFormAuthenticator)
    - For anonymous user page/1 and page/2 redirect to login form
    - page/1 visible for ADMIN and PAGE_1 roles. page/2 ADMIN and PAGE_2
    - Logged users without required roles, 403 HTTP response
    - Login form notify if user doesn't exists or wrong password

- T6: 
    - Custom templates for login form and page/x with logout link (templates/*)

- T7:
    - 300 seconds (src/AppBundle/Handler/SessionIdleHandler.php)

- Extra:
    - Applied phpcs (PSR-2), phpmd, testunit.
    - Tests (folder tests/):
        - Check page/1 for anonymous user, and then for logged. Returns 302 and 200. Page/3 (doesn't exists) returns 404
        - Test GET Rest without basic client_noauth returns 403, with basic client_noauth returns 200 and check name is Admin
        - Test POST, PUT without basic authentication returns 403
        - TEST DELETE with basic authentication with new user created, and then delete, returns 200. If doesn't exist, returns 404
