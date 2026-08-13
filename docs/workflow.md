

The system has **5 main user roles**:

```text
                    KNOWLEDGERETAIN AI
                           │
        ┌──────────────────┼──────────────────┐
        │                  │                  │
     Consumer       Customer Service    Maintenance
                                           Department
                                              │
                                  ┌───────────┴───────────┐
                                  │                       │
                         Maintenance Manager      Maintenance Technician
```

And one administrative role:

```text
                         Administrator
                              │
                    System Configuration
                    User & Role Management
                    Master Data
                    System Monitoring
```

The actual maintenance case flows like this:

```text
CONSUMER
   │
   │ Submit complaint
   ▼
CUSTOMER SERVICE
   │
   │ Review / verify
   ├──────────────► Reject
   │
   ▼
VERIFIED COMPLAINT
   │
   ▼
MAINTENANCE MANAGER
   │
   │ Assess & assign technician
   ▼
ASSIGNED MAINTENANCE CASE
   │
   ▼
MAINTENANCE TECHNICIAN
   │
   │ Perform actual repair
   │ Document work
   ▼
MAINTENANCE REPORT
   │
   ▼
MAINTENANCE MANAGER / SUPERVISOR
   │
   │ Review / validate
   ├──────────────► Return for correction
   │
   ▼
APPROVED / COMPLETED CASE
   │
   ├──────────────► KNOWLEDGE REPOSITORY
   │
   └──────────────► AI REPAIR CASE RECOMMENDATION
```

That is the workflow I recommend you build.

---

# 2. Role 1 — Administrator

The Administrator is **not responsible for handling individual maintenance complaints**.

The Admin manages the system itself.

## Administrator Dashboard

The dashboard can show:

```text
System Overview

Users
Technicians
Consumers
Customer Service
Maintenance Managers

Complaints
Pending
Verified
Rejected
Completed

Maintenance
Active Cases
Completed Cases
Pending Reports

Knowledge Repository
Total Articles
Repair Cases

AI
Recommendation Usage
```

## Admin Features

### A. User Management

```text
Admin
 └── Users
      ├── View Users
      ├── Create User
      ├── Edit User
      ├── Activate / Deactivate
      ├── Reset Password
      └── Delete / Archive
```

Roles:

```text
Administrator
Customer Service
Maintenance Manager
Maintenance Technician
Consumer
```

### B. Role & Permission Management

Control what each role can access.

Example:

| Feature              |  Admin |   CS | Manager |    Technician |        Consumer |
| -------------------- | -----: | ---: | ------: | ------------: | --------------: |
| User management      |      ✅ |    ❌ |       ❌ |             ❌ |               ❌ |
| Complaints           |      ✅ |    ✅ |       ✅ | Assigned only |             Own |
| Assign technician    |      ✅ |    ❌ |       ✅ |             ❌ |               ❌ |
| Maintenance report   |   View | View |  Review |        Create | View own status |
| Knowledge repository | Manage | View |  Manage |    Contribute |               ❌ |
| AI recommendation    |   View |    ❌ |       ✅ |    Maybe view |               ❌ |

### C. Master Data

Admin can manage:

```text
Complaint Categories
Priority Types
Maintenance Types
Equipment
Facilities
System Settings
```

Later, these should be based on the actual documents from Sagay Water District.

---

# 3. Role 2 — Consumer

The Consumer starts the **business workflow**.

## Consumer Flow

```text
Login
  │
  ▼
Consumer Dashboard
  │
  ├── Submit Complaint
  ├── My Complaints
  ├── Complaint Details
  └── Profile
```

## Submit Complaint

Consumer enters:

```text
Complaint Category
Subject
Description
Address
Landmark
Location
Photo
Contact information
```

Then:

```text
SUBMIT
   ↓
Pending
```

The consumer receives:

> Complaint submitted successfully.

The system generates:

```text
Complaint No.
CMP-000001
```

---

# 4. Consumer Complaint Tracking

Consumer can see:

```text
CMP-000001

No Water Supply

Status:
Pending
```

After Customer Service verification:

```text
Verified
```

After Maintenance Manager assignment:

```text
Assigned
```

During repair:

```text
Under Maintenance
```

After completion:

```text
Completed
```

If rejected:

```text
Rejected
```

The consumer should **not see internal technical information** such as:

* technician internal notes
* root cause analysis
* internal validation comments
* supervisor comments
* AI internal similarity scores

They can see appropriate public-facing information such as:

```text
Complaint Status
Assigned / Under Maintenance
Maintenance Completed
Completion Date
Completion Remarks
```

---

# 5. Role 3 — Customer Service

This is where your current work belongs.

Customer Service is responsible for **receiving, checking, verifying, and forwarding complaints**.

## Customer Service Dashboard

```text
Customer Service Dashboard

Pending Complaints       12
Verified Complaints       8
Rejected Complaints       2
Assigned Complaints       5
Completed Complaints     30
```

---

# 6. Customer Service Complaint Workflow

```text
Consumer submits complaint
          │
          ▼
Customer Service receives it
          │
          ▼
Review complaint
          │
       ┌──┴──┐
       │     │
    Invalid Valid
       │     │
       ▼     ▼
   REJECT  VERIFY
             │
             ▼
      Maintenance Manager
```

## Customer Service Features

### Complaints

```text
customer-service/
    complaints/
        index.blade.php
        show.blade.php
        edit.blade.php
```

### Functions

```text
View complaints
Search
Filter
View complaint
Edit complaint
Verify complaint
Reject complaint
Forward to Maintenance
```

Your existing complaint page is already doing a lot of this.

---

# 7. Complaint Status Lifecycle

I recommend we standardize the status values.

```text
Pending
   ↓
Verified
   ↓
Assigned
   ↓
In Progress
   ↓
Report Submitted
   ↓
Under Review
   ↓
Approved
   ↓
Completed
```

Alternative paths:

```text
Pending
   ↓
Rejected
```

or:

```text
Under Review
   ↓
Needs Correction
   ↓
In Progress / Report Submitted
```

This is much better than simply:

```text
Pending → Completed
```

because your capstone needs to demonstrate an actual maintenance management process.

---

# 8. Role 4 — Maintenance Manager

This role is extremely important.

The Maintenance Manager receives **verified complaints**.

## Maintenance Manager Dashboard

Example:

```text
Maintenance Manager Dashboard

Verified Complaints       8
Unassigned Cases          4
Assigned Cases            6
In Progress               3
Reports for Review        2
Completed Today           5
```

---

# 9. Maintenance Manager Workflow

```text
Verified Complaint
       │
       ▼
Manager Reviews Case
       │
       ├── Priority
       ├── Category
       ├── Location
       ├── Description
       └── Previous Cases / AI Recommendation
       │
       ▼
Assign Technician
       │
       ▼
Technician receives case
```

This is where **AI Repair Case Recommendation** becomes very useful later.

For example:

> Similar historical cases found:

```text
Case #CMP-000142
"No water supply in Zone 3"

Similarity: 91%

Previous Diagnosis:
Damaged service valve

Previous Repair:
Valve replacement

Materials:
PVC valve
Thread seal tape
```

The manager can use this information when assigning and planning the repair.

---

# 10. Maintenance Manager Features

```text
Maintenance Manager
│
├── Dashboard
│
├── Verified Complaints
│
├── Maintenance Cases
│
├── Assign Technician
│
├── Monitor Maintenance
│
├── Review Technician Reports
│
├── Approve / Return Reports
│
├── Maintenance History
│
├── Knowledge Repository
│
└── AI Recommendations
```

---

# 11. Role 5 — Maintenance Technician

This is the part we're currently working on.

The technician should **not simply have "Mark Complete."**

The proper workflow is:

```text
Manager assigns complaint
        │
        ▼
Technician receives assignment
        │
        ▼
View Complaint
        │
        ▼
Start Maintenance
        │
        ▼
Perform Repair
        │
        ▼
Fill Maintenance Report
        │
        ▼
Submit Report
        │
        ▼
Manager Review
```

---

# 12. Technician Dashboard

Something like:

```text
Technician Dashboard

Assigned Cases             5
In Progress                2
Reports Submitted          1
Completed                  8
```

Then:

```text
My Maintenance Cases

CMP-000001
No Water Supply
Priority: High
Location: Sagay City

[View] [Start Maintenance]
```

---

# 13. Technician Complaint View

The technician should see:

```text
Complaint Information

Complaint No:
CMP-000001

Category:
Water Supply

Subject:
No Water Supply

Description:
Water leak

Location:
Sagay City

Landmark:
Near Sagay Public Market

Photo:
[View Photo]

Coordinates:
10.8953451, 123.415314
```

Then:

```text
Maintenance Actions

[ Start Maintenance ]
```

Once started:

```text
Maintenance Status: In Progress

Started:
Aug 13, 2026 8:30 AM

[ Open Maintenance Report ]
```

---

# 14. Technician Maintenance Report

This is what we need to finish before calling Step 1 complete.

### Section 1 — Diagnosis

```text
Diagnosis
[____________________________]
```

Example:

> Damaged water service valve.

### Section 2 — Root Cause

```text
Root Cause
[____________________________]
```

Example:

> Valve deterioration due to prolonged usage.

### Section 3 — Work Performed

```text
Work Performed
[____________________________]
```

Example:

> Isolated water line and removed damaged valve.

### Section 4 — Repair Procedure

```text
Repair Procedure
[____________________________]
```

Example:

> Shut off main line, removed damaged valve, installed replacement valve, tested connection.

---

# 15. Materials / Parts / Tools

The report should record:

```text
Materials Used
[____________________________]

Parts Replaced
[____________________________]

Tools Used
[____________________________]
```

For your **first implementation**, these can simply be text fields.

Later, if needed:

```text
Materials
 ├── PVC pipe
 ├── PVC elbow
 └── Thread seal tape
```

---

# 16. Technician Notes

```text
Technician Notes
[________________________________]
```

And:

```text
Completion Remarks
[________________________________]
```

---

# 17. Before / After Photos

Technician uploads:

```text
Before Repair
[ Upload Photo ]

After Repair
[ Upload Photo ]
```

This gives you valuable evidence.

---

# 18. Submit Maintenance Report

Instead of:

```text
[Mark Complete]
```

we should eventually use:

```text
[Save Draft]

[Submit Maintenance Report]
```

Submitting changes:

```text
In Progress
      ↓
Report Submitted
```

Then the Manager reviews it.

This is a much stronger system design.

---

# 19. Maintenance Manager Report Validation

Manager sees:

```text
Maintenance Report

Diagnosis
Root Cause
Work Performed
Repair Procedure
Materials
Parts
Tools
Photos
Technician Notes
Completion Remarks
```

Then:

```text
        ┌─────────────────────┐
        │   Manager Review    │
        └──────────┬──────────┘
                   │
            ┌──────┴──────┐
            ▼             ▼
        APPROVE        RETURN
            │             │
            ▼             ▼
       COMPLETED      CORRECTION
```

If returned:

```text
Manager Feedback:
"Please provide more details about the replaced valve."
```

Technician edits the report and resubmits.

---

# 20. Maintenance History / Timeline

Once reports are properly implemented, we build the history.

Example:

```text
Maintenance History

● Aug 10
Complaint Submitted

● Aug 10
Complaint Verified

● Aug 11
Assigned to Technician

● Aug 12
Maintenance Started

● Aug 12
Repair Completed

● Aug 12
Maintenance Report Submitted

● Aug 13
Report Approved

● Aug 13
Case Completed
```

This should be generated from your existing records wherever possible.

---

# 21. Knowledge Repository

This is where your system becomes more than a normal CRUD maintenance system.

Approved maintenance cases become **organizational knowledge**.

Example:

```text
Knowledge Repository

Repair Case #142

Problem:
No Water Supply

Category:
Water Supply

Diagnosis:
Damaged Service Valve

Root Cause:
Valve deterioration

Repair:
Replaced service valve

Materials:
PVC valve
Thread seal tape

Tools:
Pipe wrench

Result:
Water supply restored
```

The repository can contain:

```text
Maintenance Procedures
SOPs
Repair Cases
Technical Documents
Equipment Information
Maintenance Guidelines
Troubleshooting Guides
```

---

# 22. AI Repair Case Recommendation

This should come **after you have actual historical maintenance data**.

The process:

```text
New Complaint
      │
      ▼
Text Processing
      │
      ▼
TF-IDF / Sentence Embedding
      │
      ▼
Similarity Calculation
      │
      ▼
Historical Maintenance Cases
      │
      ▼
Top Similar Cases
```

Example:

Consumer reports:

> "No water supply in our area. There is also a leak near the service connection."

AI searches previous cases.

Result:

```text
Recommended Previous Cases

1. CMP-000142
Similarity: 92%

Problem:
No water supply + service connection leak

Diagnosis:
Damaged service valve

Repair:
Valve replacement

2. CMP-000087
Similarity: 86%

Diagnosis:
Broken service pipe

Repair:
Pipe replacement
```

The Maintenance Manager can then use these recommendations.

---

# 23. AI Should NOT Automatically Repair the Problem

For your capstone, I strongly recommend the AI be presented as:

> **Decision-support / repair case recommendation**

Not:

> AI automatically diagnoses and fixes the problem.

The human technician/manager remains responsible for the actual decision.

So:

```text
AI
 ↓
Recommend similar historical cases
 ↓
Manager evaluates
 ↓
Technician performs actual repair
```

That's much more defensible academically.

---

# 24. Recommended Laravel Folder Structure

Your project can eventually look like:

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── UserController.php
│   │   │   └── ...
│   │   │
│   │   ├── CustomerService/
│   │   │   ├── DashboardController.php
│   │   │   └── ComplaintController.php
│   │   │
│   │   ├── MaintenanceManager/
│   │   │   ├── DashboardController.php
│   │   │   ├── MaintenanceController.php
│   │   │   ├── AssignmentController.php
│   │   │   └── ReportReviewController.php
│   │   │
│   │   ├── MaintenanceTechnician/
│   │   │   ├── DashboardController.php
│   │   │   ├── MaintenanceController.php
│   │   │   └── MaintenanceReportController.php
│   │   │
│   │   └── Consumer/
│   │       ├── DashboardController.php
│   │       ├── ComplaintController.php
│   │       └── ProfileController.php
```

---

# 25. Blade Structure

Based on your current project structure:

```text
resources/views/

├── admin/
│   ├── dashboard.blade.php
│   ├── users/
│   └── settings/
│
├── customer-service/
│   ├── dashboard.blade.php
│   └── complaints/
│       ├── index.blade.php
│       ├── show.blade.php
│       └── edit.blade.php
│
├── maintenance-manager/
│   ├── dashboard.blade.php
│   ├── complaints/
│   ├── maintenance/
│   └── reports/
│
├── maintenance-technician/
│   ├── dashboard.blade.php
│   ├── maintenance/
│   │   ├── index.blade.php
│   │   ├── show.blade.php
│   │   ├── report.blade.php
│   │   └── print.blade.php
│   └── profile.blade.php
│
└── consumer/
    ├── dashboard.blade.php
    ├── complaints/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   └── show.blade.php
    └── profile.blade.php
```

Your actual names can differ; this is the logical organization.

---

# 26. Important Database Relationships

Your core data model should eventually look approximately like:

```text
users
 │
 ├───────────────┐
 │               │
 ▼               ▼
Consumer       Staff
 │
 ▼
complaints
 │
 ├── complaint_category
 │
 ├── customer_service
 │
 ├── assigned technician
 │
 ├── verifier
 │
 └── maintenance_report
          │
          ├── technician
          ├── materials
          ├── parts
          ├── photos
          └── timestamps
```

Then:

```text
maintenance_report
        │
        ▼
 supervisor review
        │
        ▼
 approved maintenance case
        │
        ▼
 knowledge repository
        │
        ▼
 AI recommendation engine
```

---

# 27. Complete Development Roadmap

This is the order I recommend **for your actual coding**:

### Phase 1 — Foundation

```text
1. Authentication
2. Roles
3. Permissions
4. Dashboards
5. Profile Management
```

### Phase 2 — Complaint Management

```text
6. Consumer complaint submission
7. Customer Service complaint management
8. Complaint verification
9. Complaint rejection
10. Forwarding to Maintenance
```

### Phase 3 — Maintenance Operations

```text
11. Maintenance Manager dashboard
12. Verified complaint queue
13. Technician assignment
14. Technician maintenance queue
15. Technician complaint view
```

### Phase 4 — Technician Reporting

```text
16. Start Maintenance
17. Maintenance Report
18. Diagnosis
19. Root Cause
20. Work Performed
21. Repair Procedure
22. Materials
23. Parts
24. Tools
25. Technician Notes
26. Completion Remarks
27. Before Photo
28. After Photo
29. Submit Report
```

**This is the phase we're currently in.**

### Phase 5 — Validation

```text
30. Maintenance Manager review
31. Approve report
32. Return report
33. Technician correction
34. Resubmission
35. Complete maintenance case
```

### Phase 6 — History

```text
36. Maintenance Timeline
37. Case History
38. Technician History
39. Complaint History
40. Maintenance Statistics
```

### Phase 7 — Knowledge

```text
41. Knowledge Repository
42. Approved Repair Cases
43. Maintenance Procedures
44. SOP Repository
45. Search
46. Categorization
```

### Phase 8 — AI

```text
47. Historical data preparation
48. Text preprocessing
49. TF-IDF
50. Cosine similarity
51. Similar case retrieval
52. Recommendation ranking
53. Laravel ↔ Python communication
54. AI recommendation UI
```

---

# 28. Where We Are Right Now

Based on what you've shown me, I would **not jump to Step 2 yet**.

Your current state is approximately:

```text
Consumer
   │
   ▼
Complaint
   │
   ▼
Customer Service
   │
   ▼
Verify
   │
   ▼
Maintenance Manager
   │
   ▼
Assign Technician
   │
   ▼
Technician
   │
   ├── View Complaint       ✅
   │
   ├── Maintenance Blade   ✅
   │
   ├── Print Report        ✅
   │
   └── Detailed Report     ❌  ← CURRENT TASK
```

So our immediate objective should be:

> **Finish the Technician Maintenance Report completely.**

After that:

```text
Technician Report
       ↓
Manager Validation
       ↓
Supervisor Approval
       ↓
Maintenance History
       ↓
Knowledge Repository
       ↓
AI Recommendation
```

