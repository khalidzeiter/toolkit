# Simple PHP Handlers
## Sessions Handler (sessionHandler.php)
### Usage:
```php
// Session Start
$session = new MySessionHandler();
$session->start();
// Check Session Validity
if (!$session->isValidFingerPrint()) { $session->kill(); }
// Handle $_SESSION variable
$session->name = "Your Name"; // = $_SESSION['name'] = "Your Name";
$session->age  = "Your Age";  // = $_SESSION['age']  = "Your Age";
// ...
```
## Database Handler (dbHandler.php)
### Usage:
* Change:
- Table Structure
- Primary Key Name
- Table Name
- Table Schema & Data Type
- Constructor Parameters
* Use it:
```php
// Instantiate an Object
$user = new Users("", "", "", "", "", "", "", "");
// Get Data
$user->getAll(); // Get All Data
$user->getByPK(1); // Get By Primary Key

// INSERT a new data
$user->name = "";
$user->username = ""; // etc ...
$user->create();

// Update
// 1. Get By PK (example: PK = 1)
$u = $user->getByPK(1);
// 2. Change Data
$u->name = "New Name";
$u->username = "New Username"; // etc ...
// 3. Update
$u->update();

// Delete
// 1. Get By PK (example: PK = 1)
$u = $user->getByPK(1);
// 2. Delete
$u->delete();
```
## ...
