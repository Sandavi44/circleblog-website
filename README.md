# Circle Blog 🌐

A full-featured blog application built with PHP, MySQL, HTML, CSS, and JavaScript. Users can create accounts, write blog posts with Markdown support, comment on posts, and react with likes.

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
- ✅ **User Registration** - Create new accounts with username, email, and password
- ✅ **User Login** - Secure authentication with session management
- ✅ **User Logout** - Clean session destruction
- ✅ **Password Hashing** - Secure password storage using bcrypt

### Blog Management
- ✅ **Create Posts** - Rich text editor with Markdown support
- ✅ **Read Posts** - View all posts on homepage and individual post pages
- ✅ **Update Posts** - Edit your own blog posts
- ✅ **Delete Posts** - Remove your own posts
- ✅ **Image Upload** - Add featured images to posts
- ✅ **Authorization** - Users can only edit/delete their own posts

### Social Features
- ✅ **Comments** - Users can comment on blog posts
- ✅ **Reactions/Likes** - Like posts with one-click toggle
- ✅ **View Counts** - See how many likes and comments each post has

### User Experience
- ✅ **Responsive Design** - Works on desktop, tablet, and mobile
- ✅ **Search Functionality** - Search for blog posts
- ✅ **Flash Messages** - User feedback for actions (success, error, warning)
- ✅ **Modal Authentication** - Login/signup without page reload
- ✅ **My Posts Dashboard** - View and manage your own posts

---

## 🛠️ Technologies Used

### Backend
- **PHP 7.4+** - Server-side scripting
- **MySQL 5.7+** - Database management
- **PDO** - Secure database interactions

### Frontend
- **HTML5** - Structure
- **CSS3** - Styling with custom variables and flexbox/grid
- **JavaScript (ES6+)** - Interactive functionality
- **AJAX/Fetch API** - Asynchronous requests

### Libraries & Tools
- **Boxicons** - Icon library
- **Markdown Support** - Basic Markdown to HTML conversion

---

## 📂 Project Structure

```
circle-blog/
│
├── config/                     # Configuration files
│   ├── db.php                 # Database connection
│   └── session.php            # Session management
│
├── includes/                   # Reusable components
│   └── functions.php          # Helper functions
│
├── auth/                       # Authentication
│   ├── login.php              # Login handler
│   ├── signup.php             # Signup handler
│   └── logout.php             # Logout handler
│
├── posts/                      # Blog post operations
│   ├── create.php             # Create post page
│   ├── publish.php            # Publish handler
│   ├── edit.php               # Edit post page
│   ├── update.php             # Update handler
│   ├── delete.php             # Delete handler
│   ├── view.php               # Single post view
│   └── my_posts.php           # User's posts dashboard
│
├── api/                        # API endpoints
│   ├── comments.php           # Comment operations
│   └── reactions.php          # Like/unlike operations
│
├── assets/                     # Static files
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   ├── js/
│   │   └── main.js            # Main JavaScript
│   └── images/                # Static images
│       └── logo.svg
│
├── uploads/                    # User uploaded images
│   ├── .htaccess              # Security rules
│   └── README.md
│
├── .env                        # Environment variables (DO NOT COMMIT)
├── .gitignore                 # Git ignore rules
├── index.php                  # Homepage
└── README.md                  # This file
```

---

## 🚀 Installation

### Prerequisites
- **XAMPP/WAMP/MAMP** or any PHP development environment
- **PHP 7.4 or higher**
- **MySQL 5.7 or higher**
- **Web browser** (Chrome, Firefox, Safari, Edge)

### Step 1: Clone Repository

```bash
git clone https://github.com/yourusername/circle-blog.git
cd circle-blog
```

### Step 2: Setup Database

1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`)
2. Click **SQL** tab
3. Run the SQL script from `database.sql` (provided separately)
4. This creates:
   - Database: `circle-blog`
   - Tables: `users`, `posts`, `comments`, `likes`

### Step 3: Configure Environment

1. Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```

2. Edit `.env` with your database credentials:
   ```env
   DB_HOST=localhost
   DB_NAME=circle-blog
   DB_USERNAME=root
   DB_PASSWORD=your_password_here
   ```

### Step 4: Set Permissions

Ensure the `uploads/` folder is writable:

```bash
chmod 755 uploads/
```

### Step 5: Start Server

1. Start **XAMPP/WAMP**
2. Place project in `htdocs/` folder
3. Visit: `http://localhost/circle-blog`

---

## ⚙️ Configuration

### Environment Variables (`.env`)

| Variable | Description | Default |
|----------|-------------|---------|
| `DB_HOST` | Database host | `localhost` |
| `DB_NAME` | Database name | `circle-blog` |
| `DB_USERNAME` | Database username | `root` |
| `DB_PASSWORD` | Database password | *(your password)* |
| `APP_URL` | Application URL | `http://localhost/circle-blog` |
| `APP_ENV` | Environment mode | `development` |
| `MAX_UPLOAD_SIZE` | Max file upload size (bytes) | `5242880` (5MB) |
| `ALLOWED_FILE_TYPES` | Allowed image formats | `jpg,jpeg,png,gif,webp` |

### Security Settings

- **Password Hashing**: Bcrypt algorithm (cost factor: 10)
- **Session Security**: HttpOnly cookies, no URL parameters
- **File Upload Protection**: `.htaccess` prevents PHP execution in uploads folder
- **SQL Injection Prevention**: Prepared statements with PDO
- **XSS Protection**: All user input is sanitized with `htmlspecialchars()`

---

## 📖 Usage

### Creating a Blog Post

1. **Register/Login** to your account
2. Click **"Create Blog"** in navigation
3. Fill in:
   - Title (required, max 255 characters)
   - Content (required, Markdown supported)
   - Featured Image (optional, max 5MB)
4. Click **"Publish Post"**

### Markdown Support

```markdown
# Heading 1
## Heading 2
**bold text**
*italic text*
[link text](url)
```

### Editing Posts

1. Go to **"My Blogs"**
2. Click **Edit** icon on your post
3. Make changes
4. Click **"Update Post"**

### Deleting Posts

1. Go to **"My Blogs"** or open your post
2. Click **Delete** button
3. Confirm deletion
4. Post and all associated comments/likes are removed

### Commenting

1. Open any blog post
2. Scroll to comments section
3. Type your comment
4. Click **"Post Comment"**

### Liking Posts

1. Open any blog post
2. Click the **heart icon** below the content
3. Click again to unlike

---

## 🗄️ Database Schema

### `users` Table
- `id` - Primary key
- `username` - Unique username
- `email` - Unique email
- `password` - Hashed password
- `role` - User role (user/admin)
- `created_at` - Registration timestamp

### `posts` Table
- `id` - Primary key
- `user_id` - Foreign key to users
- `title` - Post title
- `content` - Post content (Markdown)
- `image` - Featured image path
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

### `comments` Table
- `id` - Primary key
- `post_id` - Foreign key to posts
- `user_id` - Foreign key to users
- `content` - Comment text
- `created_at` - Creation timestamp

### `likes` Table
- `id` - Primary key
- `post_id` - Foreign key to posts
- `user_id` - Foreign key to users
- `reaction_type` - Type of reaction (like, love, etc.)
- `created_at` - Creation timestamp
- **UNIQUE constraint** on (post_id, user_id) - Prevents duplicate likes

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
   - `uploads/` folder: 755

6. **Test All Features**:
   - Registration
   - Login
   - Create/Edit/Delete posts
   - Comments
   - Likes

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
- GitHub: [@yourusername](https://github.com/yourusername)
- Email: your.email@example.com

---

## 🙏 Acknowledgments

- **Boxicons** for icon library
- **University of Moratuwa** for project requirements
- **Stack Overflow** community for troubleshooting help

---

## 📝 Assignment Submission Checklist

- ✅ User registration & login working
- ✅ Create, update, delete blog posts
- ✅ View all blogs on homepage
- ✅ Single blog view page
- ✅ User can only edit/delete own posts
- ✅ Responsive design
- ✅ Database with users and posts tables
- ✅ Application hosted online
- ✅ GitHub repository created
- ✅ Demonstration video recorded (3 minutes)
- ✅ PDF document with links created

---

## 🐛 Known Issues

*(List any known issues or future improvements)*

- [ ] Search functionality can be improved with full-text search
- [ ] Add pagination for blog posts
- [ ] Add rich text editor (e.g., TinyMCE)
- [ ] Add user profile pages
- [ ] Email verification for new accounts

---

## 📞 Support

If you encounter any issues:
1. Check the [Issues](https://github.com/yourusername/circle-blog/issues) page
2. Create a new issue with detailed description
3. Contact: your.email@example.com

---

**Made with ❤️ for Web Programming Course (IN2120)**#   c i r c l e b l o g - w e b s i t e  
 