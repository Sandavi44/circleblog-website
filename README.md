
# Circle Blog 🌐

A comprehensive blogging platform developed with PHP, MySQL, HTML, CSS, and JavaScript. This application allows users to create accounts, write and manage blog posts with Markdown support, engage with others through comments and likes, and enjoy a dynamic and responsive user interface.

---

## 📋 Table of Contents

- [Features](#features)
- [Technologies Used](#technologies-used)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Database Schema](#database-schema)
- [Security Features](#security-features)
- [Deployment](#deployment)
- [Screenshots](#screenshots)
- [Contributing](#contributing)
- [License](#license)

---

## ✨ Features

### User Authentication
- ✅ **User Registration**: Sign up with a username, email, and password.
- ✅ **User Login**: Secure login with session management.
- ✅ **User Logout**: Clear session data on logout.
- ✅ **Password Hashing**: All passwords are securely hashed using bcrypt.

### Blog Management
- ✅ **Create Posts**: Write posts with Markdown support.
- ✅ **Read Posts**: View blog posts on the homepage or individual post pages.
- ✅ **Edit Posts**: Modify your own blog posts.
- ✅ **Delete Posts**: Remove your own blog posts.
- ✅ **Image Upload**: Attach featured images to posts.
- ✅ **Authorization**: Users can only edit or delete their own posts.

### Social Features
- ✅ **Comments**: Users can add comments on blog posts.
- ✅ **Likes**: React to posts with likes, with the ability to toggle likes.
- ✅ **View Counts**: Track the number of likes and comments for each post.

### User Experience
- ✅ **Responsive Design**: Optimized for desktop, tablet, and mobile devices.
- ✅ **Search Functionality**: Easily search for blog posts.
- ✅ **Flash Messages**: Get user feedback on actions such as success, errors, or warnings.
- ✅ **Modal Authentication**: Register and log in without page reloads.
- ✅ **My Posts Dashboard**: A dedicated section to manage your own posts.

---

## 🛠️ Technologies Used

### Backend
- **PHP 7.4+**: Server-side scripting for dynamic content generation.
- **MySQL 5.7+**: Relational database for storing blog data.
- **PDO**: Secure database interactions to prevent SQL injection.

### Frontend
- **HTML5**: Semantic structure of the web pages.
- **CSS3**: Custom styling using CSS variables, flexbox, and grid systems.
- **JavaScript (ES6+)**: Interactive and dynamic functionality.
- **AJAX/Fetch API**: Asynchronous web requests for smoother user interactions.

### Libraries & Tools
- **Boxicons**: Icon library for better UI design.
- **Markdown Support**: Basic Markdown parsing for post content.

---

## 📂 Project Structure

```

circle-blog/
│
├── config/                     # Configuration files
│   ├── db.php                 # Database connection setup
│   └── session.php            # Session management configuration
│
├── includes/                   # Reusable components
│   └── functions.php          # Helper functions
│
├── auth/                       # Authentication logic
│   ├── login.php              # Login handler
│   ├── signup.php             # Signup handler
│   └── logout.php             # Logout handler
│
├── posts/                      # Blog post operations
│   ├── create.php             # Create post form
│   ├── publish.php            # Publish new post handler
│   ├── edit.php               # Edit existing post
│   ├── update.php             # Update post handler
│   ├── delete.php             # Delete post handler
│   ├── view.php               # Single post view
│   └── my_posts.php           # User's post dashboard
│
├── api/                        # API endpoints for comments and reactions
│   ├── comments.php           # Handles comment operations
│   └── reactions.php          # Handles like/unlike operations
│
├── assets/                     # Static files
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   ├── js/
│   │   └── main.js            # Main JavaScript file
│   └── images/                # Static images (e.g., logo)
│       └── logo.svg
│
├── uploads/                    # Folder for user-uploaded images
│   ├── .htaccess              # Security settings for file uploads
│   └── README.md              # Instructions for uploaded files
│
├── .env                        # Environment configuration (DO NOT COMMIT)
├── .gitignore                 # Git ignore rules
├── index.php                  # Homepage
└── README.md                  # Project documentation (this file)

````

---

## 🚀 Installation

### Prerequisites
- **XAMPP/WAMP/MAMP** or any other PHP development environment
- **PHP 7.4+**
- **MySQL 5.7+**
- **Web browser** (e.g., Chrome, Firefox)

### Step-by-Step Setup

1. **Clone the Repository**

```bash
git clone https://github.com/yourusername/circle-blog.git
cd circle-blog
````

2. **Setup the Database**

* Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
* Create a new database: `circle-blog`.
* Import the provided SQL file (`database.sql`) to set up the necessary tables (`users`, `posts`, `comments`, `likes`).

3. **Configure Environment**

* Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

* Update `.env` with your database credentials:

```env
DB_HOST=localhost
DB_NAME=circle-blog
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

4. **Set Folder Permissions**

Make the `uploads/` folder writable:

```bash
chmod 755 uploads/
```

5. **Start the Server**

* Start **XAMPP/WAMP**.
* Place the project in the `htdocs/` folder.
* Access the app at: `http://localhost/circle-blog`.

---

## ⚙️ Configuration

### Environment Variables (`.env`)

| Variable             | Description                          | Default Value                  |
| -------------------- | ------------------------------------ | ------------------------------ |
| `DB_HOST`            | Database host                        | `localhost`                    |
| `DB_NAME`            | Database name                        | `circle-blog`                  |
| `DB_USERNAME`        | Database username                    | `root`                         |
| `DB_PASSWORD`        | Database password                    | *(your password)*              |
| `APP_URL`            | Base URL of the application          | `http://localhost/circle-blog` |
| `APP_ENV`            | Application environment mode         | `development`                  |
| `MAX_UPLOAD_SIZE`    | Max allowed file upload size (bytes) | `5242880` (5MB)                |
| `ALLOWED_FILE_TYPES` | Allowed image types                  | `jpg,jpeg,png,gif,webp`        |

---

## 📖 Usage

### Creating a Blog Post

1. **Register/Login** to your account.
2. Navigate to the **"Create Blog"** page.
3. Enter the following:

   * Title (required)
   * Content (required, supports Markdown)
   * Featured Image (optional, max 5MB)
4. Click **"Publish Post"**.

### Editing a Post

1. Navigate to **"My Blogs"**.
2. Click the **Edit** icon next to your post.
3. Make necessary changes.
4. Click **"Update Post"**.

### Deleting a Post

1. Navigate to **"My Blogs"** or view the post directly.
2. Click **Delete**.
3. Confirm deletion.

### Commenting

1. Go to any post.
2. Scroll to the comments section.
3. Add your comment and click **"Post Comment"**.

### Liking Posts

1. Open a blog post.
2. Click the **heart icon** to like it.
3. Click again to unlike.

---

## 🗄️ Database Schema

### `users` Table

* `id`: Primary key
* `username`: Unique username
* `email`: Unique email
* `password`: Hashed password
* `role`: User role (user/admin)
* `created_at`: Timestamp

### `posts` Table

* `id`: Primary key
* `user_id`: Foreign key to users
* `title`: Post title
* `content`: Post content (Markdown)
* `image`: Featured image path
* `created_at`: Creation timestamp
* `updated_at`: Last update timestamp

### `comments` Table

* `id`: Primary key
* `post_id`: Foreign key to posts
* `user_id`: Foreign key to users
* `content`: Comment text
* `created_at`: Timestamp


### `likes` Table

* `id`: Primary key
* `post_id`: Foreign key to posts
* `user_id`: Foreign key to users
* `reaction_type`: Type of reaction (like, love, etc.)
* `created_at`: Timestamp
* **UNIQUE constraint** on `(post_id, user_id)` to prevent duplicate likes

---

## 🔒 Security Features

1. **Environment Variables** - Sensitive data in `.env` (not committed to Git)
2. **Password Hashing** - Bcrypt with `password_hash()`
3. **Prepared Statements** - SQL injection prevention
4. **XSS Protection** - Output sanitization with `htmlspecialchars()`
5. **CSRF Protection** - Token validation for forms
6. **File Upload Validation** - Type and size checks
7. **Session Security** - HttpOnly cookies, regeneration on login
8. **Authorization Checks** - Users can only edit/delete own content
9. **Upload Directory Protection** - `.htaccess` blocks PHP execution

---

## 🌐 Deployment

### Hosting Options

1. **InfinityFree** (Free)
2. **000WebHost** (Free)
3. **HostGator** (Paid)
4. **Bluehost** (Paid)

### Deployment Steps

1. **Export Database**:

```bash
mysqldump -u root -p circle-blog > database.sql
```

2. **Upload Files** via FTP/cPanel File Manager

3. **Import Database** on hosting server

4. **Update `.env`**:

```env
APP_URL=https://yourdomain.com
APP_ENV=production
DB_HOST=localhost  # May be different on host
DB_NAME=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

5. **Set Permissions**:

   * `uploads/` folder: 755

6. **Test All Features**:

   * Registration
   * Login
   * Create/Edit/Delete posts
   * Comments
   * Likes

---

## 📸 Screenshots

*(Add screenshots here after deployment)*

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👤 Author

**Your Name**

* GitHub: [@sandavi44](https://github.com/sandavi44)
* Email: [info@circleblog.com](mailto:info@circleblog.com)

---

## 🙏 Acknowledgments

* **Boxicons** for icon library
* **University of Moratuwa** for project requirements
* **Stack Overflow** community for troubleshooting help

---

## 📝 Assignment Submission Checklist

* ✅ User registration & login working
* ✅ Create, update, delete blog posts
* ✅ View all blogs on homepage
* ✅ Single blog view page
* ✅ User can only edit/delete own posts
* ✅ Responsive design
* ✅ Database with users and posts tables
* ✅ Application hosted online
* ✅ GitHub repository created
* ✅ Demonstration video recorded (3 minutes)
* ✅ PDF document with links created

---

## 🐛 Known Issues

*(List any known issues or future improvements)*

* [ ] Search functionality can be improved with full-text search
* [ ] Add pagination for blog posts
* [ ] Add rich text editor (e.g., TinyMCE)
* [ ] Add user profile pages
* [ ] Email verification for new accounts

---

## 📞 Support

If you encounter any issues:

1. Check the [Issues](https://github.com/Sandavi44/circleblog-website/issues) page
2. Create a new issue with a detailed description
3. Contact: [info@circleblog.com](mailto:info@circleblog.com)



