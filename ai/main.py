from datetime import datetime, timezone

from fastapi import FastAPI
from pydantic import BaseModel

from ai.app.assistant.service import KnowledgeAssistant
from ai.app.classification.classifier import ComplaintClassifier


app = FastAPI(
    title="Complaint Service Support",
    version="1.0.0",
    description=(
        "AI support service for the Sagay Water District "
        "Consumer Complaint Management System."
    ),
)


assistant = KnowledgeAssistant()
classifier = ComplaintClassifier()


class AssistantRequest(BaseModel):
    question: str

class ClassificationRequest(BaseModel):
    text: str


@app.get("/")
def root():
    return {
        "message": "Complai Service Support is running."
    }


@app.get("/health")
def health():
    return {
        "status": "ok",
        "service": "Complaint Service Support",
        "timestamp": datetime.now(timezone.utc).isoformat(),
    }


@app.post("/assistant/query")
def assistant_query(request: AssistantRequest):
    return assistant.answer(request.question)

@app.post("/classification/predict")
def classify_complaint(request: ClassificationRequest):
    return classifier.predict(request.text)
