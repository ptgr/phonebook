Simple example of phonebook.

# **INSTALLATION**
1) Clone repository using `git clone https://github.com/ptgr/phonebook.git`
2) Create copy of **.env.example** and rename it to **.env**
3) Open terminal and change directory to phonebook
4) Run `docker-compose up`

### **DATABASE ACCESS**   
http://localhost:8086/

System: *MySQL*  
Server: *database*  
User: *phonebook_user*  
Password: *y0r8Rv0HjshZCAi*  
Database: *db_phonebook*

# **EXAMPLES**

### **GET ALL CONTACTS**

SUPPORTED FILTER OPTION: number

> GET http://localhost:80/api/v1/contacts

> GET http://localhost:80/api/v1/contacts?number=[phone]

RESPONSE
```
[
  {
    "id": 9,
    "firstName": "...",
    "lastName": "...",
    "phoneNumbers": [
      {
        "id": 27,
        "type": "...",
        "number": "..."
      },
      {
        "id": 26,
        "type": "...",
        "number": "..."
      }
    ],
    "attributes": [
      {
        "id": 21,
        "name": "...",
        "value": "..."
      },
      {
        "id": 20,
        "name": "...",
        "value": "..."
      }
    ]
  }
]
```

### **GET SPECIFIC CONTACT**

> GET http://localhost:80/api/v1/contacts/[id]

RESPONSE

```
{
  "id": 9,
  "firstName": "...",
  "lastName": "...",
  "phoneNumbers": [
    {
      "id": 27,
      "type": "...",
      "number": "..."
    },
    {
      "id": 26,
      "type": "...",
      "number": "..."
    }
  ],
  "attributes": [
    {
      "id": 21,
      "name": "...",
      "value": "..."
    },
    {
      "id": 20,
      "name": "...",
      "value": "..."
    }
  ]
}
```

### **CREATE CONTACT**

> POST http://localhost:80/api/v1/contacts/create

PAYLOAD JSON FORMAT

```
[
  {
    "firstName": "Firstname" // Max length: 50,
    "lastName": "Lastname"  // Max length: 50,
    "phoneNumbers": [
      {
        "type": "home" // Valid values are 'home', 'work', 'personal',
        "number": "721222333" // Valid format xxx xxx xxx
      },
      {
        "type": "work",
        "number": "+420601222333" // With +420 prefix it is also valid
      }
    ],
    "attributes": [
      {
        "name": "city" // Max length: 20,
        "value": "Prague" // Max length: 30
      },
      {
        "name": "street",
        "value": "Ulice"
      }
    ]
  }
]
```

CURL
```
curl  -X POST \
  'http://localhost:80/api/v1/contacts/create' \
  --header 'Accept: */*' \
  --header 'Content-Type: application/json' \
  --data-raw '[
  {
    "firstName": "Firstname",
    "lastName": "Lastname",
    "phoneNumbers": [
      {
        "type": "home",
        "number": "721222333"
      },
      {
        "type": "work",
        "number": "+420601222333"
      }
    ],
    "attributes": [
      {
        "name": "city",
        "value": "Prague"
      },
      {
        "name": "street",
        "value": "Ulice"
      }
    ]
  }
]'
```

### **UPDATE CONTACT**

> PUT http://localhost:80/api/v1/contacts/update/[id]

PAYLOAD JSON FORMAT

```
{
  "firstName": "Firstname",    // Max length: 50
  "lastName": "Lastname",      // Max length: 50
  "phoneNumbers": [
    {
      "type": "home",           // Valid values are 'home', 'work', 'personal'
      "number": "721222333"     // Valid format xxx xxx xxx
    },
    {
      "type": "work",
      "number": "+420601222333" // With +420 prefix it is also valid
    }
  ],
  "attributes": [
    {
      "name": "city",           // Max length: 20
      "value": "Prague"         // Max length: 30
    },
    {
      "name": "street",
      "value": "Ulice"
    }
  ]
}
```

CURL
```
curl  -X PUT \
  'http://localhost:80/api/v1/contacts/update/[id]' \
  --header 'Accept: */*' \
  --header 'Content-Type: application/json' \
  --data-raw '{
  "firstName": "name first",
  "lastName": "name last",
  "phoneNumbers": [
    {
      "type": "home",
      "number": "721444666"
    }
  ],
  "attributes": [
    {
      "name": "city",
      "value": "City"
    }
  ]
}'
```
