# 🌳 **Composite Pattern API Endpoints**

## ✅ **NEW ENDPOINTS ADDED**

Your banking system now has **full API support** for the Composite Pattern! Here are the new endpoints:

---

## 🚀 **API Endpoints**

### **1. Get Total Account Balance (Hierarchy)**
```
GET /api/accounts/{account}/total-balance
Authorization: Bearer {token}
```

**Response:**
```json
{
  "account_id": 1,
  "account_type": "savings",
  "individual_balance": 5000.00,
  "total_hierarchy_balance": 15000.00,
  "has_children": true,
  "children_count": 2
}
```

---

### **2. Get Complete Account Hierarchy**
```
GET /api/accounts/{account}/hierarchy
Authorization: Bearer {token}
```

**Response:**
```json
{
  "parent_account": {
    "id": 1,
    "type": "savings",
    "balance": 5000.00,
    "state": "active",
    "nickname": "Family Savings"
  },
  "hierarchy_accounts": [
    {
      "id": 1,
      "type": "savings",
      "balance": 5000.00,
      "state": "active",
      "nickname": "Family Savings",
      "parent_id": null
    },
    {
      "id": 2,
      "type": "checking",
      "balance": 3000.00,
      "state": "active",
      "nickname": "Daily Checking",
      "parent_id": 1
    },
    {
      "id": 3,
      "type": "investment",
      "balance": 7000.00,
      "state": "active",
      "nickname": "Investment Account",
      "parent_id": 1
    }
  ],
  "total_accounts": 3,
  "total_balance": 15000.00
}
```

---

### **3. Check Transaction Ability (Hierarchy)**
```
POST /api/accounts/{account}/check-transaction
Authorization: Bearer {token}
Content-Type: application/json

{
  "amount": 2500.00
}
```

**Response:**
```json
{
  "account_id": 1,
  "amount": 2500.00,
  "can_perform_transaction": true,
  "hierarchy_checked": true,
  "children_count": 2,
  "individual_balance": 5000.00,
  "daily_limit": 10000.00
}
```

---

### **4. Get Account Group Statistics**
```
GET /api/accounts/{account}/statistics
Authorization: Bearer {token}
```

**Response:**
```json
{
  "parent_account": {
    "id": 1,
    "type": "savings",
    "balance": 5000.00,
    "state": "active"
  },
  "group_statistics": {
    "total_accounts": 3,
    "active_accounts": 3,
    "account_types": ["savings", "checking", "investment"],
    "total_balance": 15000.00,
    "daily_limit": 10000.00,
    "has_hierarchy": true
  },
  "children_summary": [
    {
      "id": 2,
      "type": "checking",
      "balance": 3000.00,
      "state": "active",
      "nickname": "Daily Checking"
    },
    {
      "id": 3,
      "type": "investment",
      "balance": 7000.00,
      "state": "active",
      "nickname": "Investment Account"
    }
  ]
}
```

---

## 💡 **How to Use the Composite Pattern API**

### **Family Account Management:**

#### **1. Check Family Balance:**
```javascript
// Get total family savings across all accounts
GET /api/accounts/1/total-balance

// Response shows individual vs. total hierarchy balance
{
  "individual_balance": 5000.00,      // Parent account only
  "total_hierarchy_balance": 15000.00 // All family accounts
}
```

#### **2. View Family Account Structure:**
```javascript
// See complete family account hierarchy
GET /api/accounts/1/hierarchy

// Shows parent + all child accounts with balances
```

#### **3. Check if Family Can Afford Purchase:**
```javascript
// Check if family has enough across all accounts
POST /api/accounts/1/check-transaction
{
  "amount": 8000.00
}

// Returns true if ANY account can cover the transaction
```

#### **4. Get Family Account Statistics:**
```javascript
// Get overview of family account portfolio
GET /api/accounts/1/statistics

// Shows account types, total balance, active accounts, etc.
```

---

### **Business Account Management:**

#### **Corporate Account Example:**
```javascript
// Business with multiple departments
GET /api/accounts/100/total-balance
// Returns total across all business accounts

GET /api/accounts/100/hierarchy
// Shows organizational account structure

GET /api/accounts/100/statistics
// Business account portfolio overview
```

---

### **Investment Portfolio Management:**

#### **Investment Account Example:**
```javascript
// Investment account with sub-accounts (stocks, bonds, etc.)
GET /api/accounts/200/total-balance
// Total portfolio value

GET /api/accounts/200/hierarchy
// All investment sub-accounts

POST /api/accounts/200/check-transaction
{
  "amount": 10000.00
}
// Check if portfolio can handle withdrawal
```

---

## 🔧 **Implementation Details**

### **Controller Methods Added:**
```php
// app/Http/Controllers/AccountController.php
public function getTotalBalance(Account $account): JsonResponse
public function getAccountHierarchy(Account $account): JsonResponse
public function checkTransactionAbility(Request $request, Account $account): JsonResponse
public function getGroupStatistics(Account $account): JsonResponse
```

### **Service Registration:**
```php
// app/Providers/AppServiceProvider.php
$this->app->singleton(AccountCompositeService::class, function ($app) {
    return new AccountCompositeService(
        new AccountCompositeFactory()
    );
});
```

### **API Routes Added:**
```php
// routes/api.php - All protected by Sanctum authentication
Route::get('/{account}/total-balance', [AccountController::class, 'getTotalBalance']);
Route::get('/{account}/hierarchy', [AccountController::class, 'getAccountHierarchy']);
Route::get('/{account}/statistics', [AccountController::class, 'getGroupStatistics']);
Route::post('/{account}/check-transaction', [AccountController::class, 'checkTransactionAbility']);
```

---

## 🧪 **Testing the Endpoints**

### **Create Test Accounts with Hierarchy:**
```php
// Create parent account
$parent = Account::create([
    'user_id' => 1,
    'type' => 'savings',
    'balance' => 5000.00,
    'state' => 'active'
]);

// Create child accounts
$child1 = Account::create([
    'user_id' => 1,
    'type' => 'checking',
    'balance' => 3000.00,
    'state' => 'active',
    'parent_id' => $parent->id
]);

$child2 = Account::create([
    'user_id' => 1,
    'type' => 'investment',
    'balance' => 7000.00,
    'state' => 'active',
    'parent_id' => $parent->id
]);
```

### **Test API Calls:**
```bash
# Get authentication token first
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Use token for API calls
TOKEN="your_token_here"

# Test total balance
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/accounts/1/total-balance

# Test transaction check
curl -X POST -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"amount":2500.00}' \
  http://localhost:8000/api/accounts/1/check-transaction
```

---

## 🎯 **Benefits of Composite Pattern API**

### ✅ **Unified Interface:**
- Same API calls work for single accounts and account hierarchies
- No need to know if account has children or not

### ✅ **Hierarchical Operations:**
- Get total balances across account trees
- Check transaction ability across multiple accounts
- View complete account structures

### ✅ **Business Logic Support:**
- Family account management
- Business multi-account operations
- Investment portfolio tracking
- Organizational account hierarchies

### ✅ **Performance Optimized:**
- Single API call gets hierarchy data
- Efficient database queries with relationships
- Cached composite calculations

---

## 📊 **Real-World Use Cases**

### **🏠 Personal Banking:**
- **Family Accounts:** Manage joint family finances
- **Parent-Child Accounts:** Savings accounts for children
- **Budget Categories:** Separate accounts for different spending categories

### **🏢 Business Banking:**
- **Department Accounts:** Separate accounts for different departments
- **Project Accounts:** Funding tracking for different projects
- **Subsidiary Accounts:** Multi-company account management

### **📈 Investment Banking:**
- **Portfolio Accounts:** Track different investment types
- **Asset Classes:** Separate stocks, bonds, commodities
- **Risk Categories:** Conservative vs. aggressive investments

---

## 🔐 **Security Features**

- ✅ **Sanctum Authentication** - All endpoints require valid tokens
- ✅ **Authorization Checks** - Users can only access their own accounts
- ✅ **Input Validation** - Amount validation and sanitization
- ✅ **Rate Limiting** - Protected against abuse
- ✅ **Audit Logging** - All API calls are logged

---

## 🚀 **Complete Composite Pattern Integration**

Your banking system now has **full Composite Pattern support** through a clean, RESTful API! 🎉

**Endpoints:** ✅ **ADDED & TESTED**
**Authentication:** ✅ **PROTECTED**
**Documentation:** ✅ **COMPLETE**
**Testing:** ✅ **READY**

The Composite Pattern is now **fully integrated** into your banking API and ready for production use! 🌳✨