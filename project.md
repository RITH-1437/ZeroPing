# ZeroPing Framework

## Phase 8 — Complete ORM

### Completed Features

- Mass Assignment
- Automatic Timestamps
- Attribute Casting
- Relationships (HasOne, HasMany, BelongsTo, BelongsToMany)
- Lazy Loading
- Query Scopes
- Soft Deletes
- Model Events
- Accessors & Mutators
- Pagination
- Collections
- Query Builder Improvements
- Model Utilities
- Custom Exceptions
- ORM Testing Command

### Architecture

The ORM is designed to be a modern Active Record implementation. It is built on top of the existing `QueryBuilder` and follows SOLID principles. The core components of the ORM are:

- **Model:** The base class for all models. It provides the Active Record API.
- **Builder:** The query builder for the ORM. It provides a fluent interface for building queries.
- **Collection:** A custom collection class that provides a fluent interface for working with arrays of data.
- **Hydrator:** Responsible for creating model instances from database results.
- **Persister:** Responsible for saving and updating models in the database.
- **Concerns:** A set of traits that provide additional functionality to models, such as mass assignment, timestamps, and soft deletes.
- **Relations:** A set of classes that define the relationships between models.
- **Exceptions:** A set of custom exceptions for the ORM.

### ORM Folder Structure

```
Core
└── ORM
    ├── Builder.php
    ├── Collection.php
    ├── Hydrator.php
    ├── Model.php
    ├── Persister.php
    ├── Pagination
    │   └── Paginator.php
    ├── Relations
    │   ├── Relation.php
    │   ├── HasOne.php
    │   ├── HasMany.php
    │   ├── BelongsTo.php
    │   └── BelongsToMany.php
    ├── Concerns
    │   ├── HasAttributes.php
    │   ├── GuardsAttributes.php
    │   ├── HasRelationships.php
    │   └── HasTimestamps.php
    └── Exceptions
        ├── ModelNotFoundException.php
        ├── MassAssignmentException.php
        └── RelationNotFoundException.php
```

### Example Code

```php
// Get all users
User::all();

// Find a user by ID
User::find(1);

// Find a user by email
User::where('email', 'admin@example.com')->first();

// Create a new user
User::create([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 'password',
]);

// Update a user
$user = User::find(1);
$user->update([
    'name' => 'John Smith',
]);

// Delete a user
$user = User::find(1);
$user->delete();

// Get a user's coffees
$user = User::find(1);
$user->coffees;

// Paginate users
User::latest()->paginate(10);

// Get all users, including soft-deleted users
User::withTrashed()->get();

// Find a user by ID or throw an exception
User::findOrFail(1);
```

### Roadmap

- [x] Phase 1: Project Setup
- [x] Phase 2: Routing
- [x] Phase 3: Controllers
- [x] Phase 4: Views
- [x] Phase 5: Database
- [x] Phase 6: Console
- [x] Phase 7: Service Providers
- [x] Phase 8: Complete ORM
- [ ] Phase 9: Authentication
- [ ] Phase 10: Authorization
- [ ] Phase 11: Validation
- [ ] Phase 12: Error Handling
- [ ] Phase 13: Logging
- [ ] Phase 14: Testing
- [ ] Phase 15: Deployment
```