Consumer:


Consumer Login
   ↓
Submit Complaint
   ↓
Select Category
   ↓
Enter Subject
   ↓
Enter Description
   ↓
Enter Address
   ↓
Pin Map Location
   ↓
Upload Photo
   ↓
Submit



My Complaints
   ↓
View Complaint
   ↓
Map appears
   ↓
Technician team appears when assigned
   ↓
Edit Complaint while Pending



Customer Service:

Customer Service
       ↓
Complaints
       ↓
View consumer complaint
       ↓
Verify complaint
       ↓
Set priority
       ↓
Add verification reason/details
       ↓
Verified


Maintenance Manager:
Verified
   ↓
Maintenance Manager
   ↓
Complaint appears in Assignment
   ↓
Select 1–3 technicians
   ↓
Assign
   ↓
Status = Assigned


Technician:

Technician
    ↓
Assigned Complaints
    ↓
Complaint appears
    ↓
Start Maintenance
    ↓
Status = In Progress
    ↓
Maintenance Report
    ↓
Submit Report
    ↓
Complaint = Completed
    ↓
Report = Pending Review

Manager Report Review

Maintenance Manager
       ↓
Maintenance Report
       ↓
Review
   ↙          ↘
Return       Approve
  ↓             ↓
Technician    Finalized
corrects


AI Complaint Support:

Consumer complaint
       ↓
AI processing
       ↓
┌──────────────────────────┐
│ Category suggestion      │
│ Urgency suggestion       │
│ Complaint summary        │
│ Suggested response       │
│ FAQ/service guidance     │
│ Language assistance      │
└──────────────────────────┘
       ↓
Customer Service
       ↓
Human decision

✅ FastAPI foundation
🔵 Knowledge-based assistant ← we are here
AI confidence/threshold handling
Connect assistant to Laravel
Consumer AI Assistant interface
Use existing complaint_categories for AI complaint classification
AI priority recommendation
Customer Service AI recommendation panel
Evaluation/testing of the AI model



.\ai\.venv\Scripts\Activate.ps1

uvicorn ai.main:app --reload --port 8001




DONE
✓ Complaint Classification / Analysis
✓ Urgency Analysis
✓ Related Complaint Detection
✓ Plumber Recommendation

→ Natural-Language Consumer Complaint Intake
     ↓
→ Store AI analysis/audit data in Laravel
     ↓
→ AI analysis in Customer Service workflow
     ↓
NEXT


→ Improve Accomplishment / Manager Review workflow
     ↓
→ Historical completed-case knowledge retrieval
     ↓
→ Dashboard AI metrics
     ↓
→ Real SWD data evaluation/testing
     ↓
→ Final capstone evaluation + documentation


That lets you demonstrate that the system is AI-assisted with human confirmation, and later measure whether the classifier was correct using actual SWD cases.
So I would not add another unrelated AI model now. Our next implementation should be the Consumer natural-language complaint intake, reusing the classifier and triage engine we've already built. After that, we'll add persistence/audit history so your AI results become measurable rather than only displayed on screen.
ok proceed
