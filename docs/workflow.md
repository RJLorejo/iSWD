                    iSWD
                     │
       ┌─────────────┴─────────────┐
       │                           │
   CONSUMER                   WATER DISTRICT
       │                           │
       ▼                           ▼
Submit Complaint            Customer Service
       │                           │
       ▼                           ▼
AI-Assisted Support        Review / Verify
       │                           │
       └──────────────┬────────────┘
                      ▼
             Complaint Processing
                      │
          ┌───────────┴───────────┐
          │                       │
      Service/Billing        Maintenance
       Concern                  Issue
          │                       │
          ▼                       ▼
   Customer Service       Manager/Supervisor
                                  │
                                  ▼
                           Technician
                                  │
                                  ▼
                             Work Order
                                  │
                                  ▼
                              Resolution
                                  │
                                  ▼
                        Consumer Notification






Consumer
   │
   ├── Select concern
   │
   ├── AI-assisted guidance
   │
   ├── Submit complaint
   │
   └── View status / response
             │
             ▼
       Customer Service
             │
             ├── AI suggested response
             ├── AI complaint classification
             └── Human decision




Great. Since the **Complaint Management + multi-technician assignment + consumer visibility + printable report** are now working, I recommend we move to the next important part of your actual workflow.

## Next: Technician Complaint Workflow

Your system currently has:

**Consumer**
→ Submit Complaint
→ **Customer Service**
→ Verify Complaint
→ **Maintenance Manager**
→ Assign 1–3 Technicians
→ **Technician**

So the next module should be the **Maintenance Technician side**.

### Recommended development order

| Step  | Module                               | Status      |
| ----- | ------------------------------------ | ----------- |
| 1     | Consumer Registration/Login          | ✅           |
| 2     | Consumer Complaint Submission        | ✅           |
| 3     | Customer Service Verification        | ✅           |
| 4     | Manager Complaint Management         | ✅           |
| 5     | Multi-Technician Assignment          | ✅           |
| 6     | Consumer sees assigned technicians   | ✅           |
| 7     | Printable Complaint Report           | ✅           |
| **8** | **Technician Complaint Dashboard**   | **⬅️ Next** |
| 9     | Technician views assigned complaint  | Next        |
| 10    | Start Maintenance / mark In Progress | Next        |
| 11    | Maintenance Report                   | Next        |
| 12    | Before/After Photos                  | Next        |
| 13    | Manager Review/Validation            | Next        |
| 14    | Consumer sees resolution             | Next        |
| 15    | Notifications                        | Next        |
| 16    | Knowledge Repository                 | Later       |
| 17    | AI Complaint Assistance              | Later       |
| 18    | Reports & Analytics                  | Later       |

# Step 8 — Technician Dashboard

The technician should **only see complaints assigned to them**.

For example:

```text
Maintenance Technician
────────────────────────────────────

Dashboard

Good morning, Juan Dela Cruz

┌──────────────┐ ┌──────────────┐
│ Assigned     │ │ In Progress  │
│      5       │ │      2       │
└──────────────┘ └──────────────┘

┌──────────────┐ ┌──────────────┐
│ Completed    │ │ Today's Jobs │
│      8       │ │      3       │
└──────────────┘ └──────────────┘


Assigned Complaints

CMP-2026-005
Low Water Pressure
Purok 3, Brgy. Poblacion

Team:
• Juan Dela Cruz
• Pedro Santos

Status: Assigned

[View Complaint]


CMP-2026-006
Pipe Leak
Brgy. Rizal

Team:
• Juan Dela Cruz
• Maria Reyes

Status: In Progress

[Continue Maintenance]
```

The important part is that **Juan should not see every complaint in the system**.

We use:

```php
$complaints = auth()->user()
    ->assignedComplaints()
    ->with([
        'consumer',
        'category',
        'technicians',
    ])
    ->latest()
    ->paginate(10);
```

That uses the `complaint_technicians` pivot we just established.

---

# Then Technician Complaint Details

When the technician clicks **View Complaint**, show:

### Complaint Information

* Complaint number
* Consumer
* Account number
* Category
* Subject
* Description
* Address
* Landmark
* Location

### Assigned Team

```text
Assigned Maintenance Team

👤 Juan Dela Cruz
👤 Pedro Santos
👤 Maria Reyes
```

### Current Status

```text
Assigned
```

And the main action:

```text
[ Start Maintenance ]
```

Once clicked:

```text
Assigned
   ↓
In Progress
```

---

# Then Maintenance Report

After the technician/team finishes the work:

```text
Maintenance Report

Problem Found
[____________________________]

Action Taken
[____________________________]

Materials Used
[____________________________]

Work Performed
[____________________________]

Recommendation
[____________________________]

Before Photo
[ Upload ]

After Photo
[ Upload ]

[ Submit Maintenance Report ]
```

Then:

```text
Technician
    ↓
Submit Report
    ↓
Manager Review
    ↓
Approved
    ↓
Complaint Completed
    ↓
Consumer notified
```

### One important design decision

Because **multiple technicians can work on the same complaint**, I recommend that the maintenance report belong to the **complaint/team**, rather than forcing every technician to create a completely separate report.

That prevents this situation:

```text
Juan → Report #1
Pedro → Report #2
Maria → Report #3
```

for one repair.

Instead:

```text
Complaint
   │
   ├── Assigned technicians
   │     ├── Juan
   │     ├── Pedro
   │     └── Maria
   │
   └── One Maintenance Report
         ├── Problem Found
         ├── Work Performed
         ├── Materials
         ├── Before Photos
         └── After Photos
```

Much cleaner for your capstone and closer to how a team handles one field problem.

## So let's build the **Technician module next**

I recommend we do it in this exact order:

**1. Technician routes → 2. Technician Controller → 3. Technician dashboard → 4. Assigned complaint list → 5. Complaint details → 6. Start Maintenance → 7. Maintenance Report.**

And we should reuse your existing `complaint_technicians` architecture rather than creating another technician-assignment system.
