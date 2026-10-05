from datetime import datetime, timezone

from fastapi import FastAPI, HTTPException
from pydantic import BaseModel, Field

from ai.app.assistant.service import (
    KnowledgeAssistant,
)
from ai.app.classification.classifier import (
    ComplaintClassifier,
)
from ai.app.triage.analyzer import (
    ComplaintAnalyzer,
)
from ai.app.similarity.detector import (
    SimilarComplaintDetector,
)
from ai.app.assignment.recommender import (
    PlumberAssignmentRecommender,
)


app = FastAPI(
    title="iSWD AI Complaint Support Service",
    version="2.1.0",
    description=(
        "AI-assisted complaint analysis service "
        "for the Sagay Water District iSWD system."
    ),
)


assistant = KnowledgeAssistant()

classifier = ComplaintClassifier()

complaint_analyzer = ComplaintAnalyzer()

similar_complaint_detector = (
    SimilarComplaintDetector()
)

plumber_assignment_recommender = (
    PlumberAssignmentRecommender()
)


class AssistantRequest(BaseModel):
    question: str = Field(
        min_length=1,
        max_length=1000,
    )


class ClassificationRequest(BaseModel):
    text: str = Field(
        min_length=1,
        max_length=5000,
    )


class ComplaintAnalysisRequest(BaseModel):
    description: str = Field(
        min_length=10,
        max_length=5000,
    )


class SimilarComplaintItem(BaseModel):
    id: int

    complaint_no: str

    description: str

    division_id: int | None = None

    complaint_category_id: int | None = None

    status: str | None = None

    latitude: float | None = None

    longitude: float | None = None

    hours_difference: float | None = None


class SimilarComplaintRequest(BaseModel):
    complaint: SimilarComplaintItem

    candidates: list[SimilarComplaintItem]

    limit: int = Field(
        default=5,
        ge=1,
        le=20,
    )


class PlumberServiceArea(BaseModel):
    id: int

    name: str


class PlumberActiveAssignment(BaseModel):
    complaint_id: int

    complaint_no: str

    complaint_type: str | None = None

    status: str

    latitude: float | None = None

    longitude: float | None = None


class PlumberRecentAssignment(BaseModel):
    complaint_id: int

    complaint_no: str

    complaint_type: str | None = None

    latitude: float | None = None

    longitude: float | None = None

    completed_at: datetime | None = None


class PlumberCandidate(BaseModel):
    id: int

    name: str

    service_area: PlumberServiceArea | None = None

    active_workload: int = Field(
        default=0,
        ge=0,
    )

    active_assignments: list[
        PlumberActiveAssignment
    ] = Field(
        default_factory=list
    )

    recent_assignments: list[
        PlumberRecentAssignment
    ] = Field(
        default_factory=list
    )


class AssignmentComplaint(BaseModel):
    id: int

    complaint_no: str

    complaint_type: str | None = None

    division: str | None = None

    latitude: float | None = None

    longitude: float | None = None


class PlumberRecommendationRequest(
    BaseModel
):
    complaint: AssignmentComplaint

    plumbers: list[PlumberCandidate]

    limit: int = Field(
        default=5,
        ge=1,
        le=20,
    )


@app.get("/")
def root():
    return {
        "message": (
            "iSWD AI Complaint Support "
            "Service is running."
        ),
        "version": "2.1.0",
    }


@app.get("/health")
def health():
    return {
        "status": "ok",
        "service": (
            "iSWD AI Complaint Support Service"
        ),
        "version": "2.1.0",
        "timestamp": datetime.now(
            timezone.utc
        ).isoformat(),
    }


@app.post("/assistant/query")
def assistant_query(
    request: AssistantRequest,
):
    return assistant.answer(
        request.question
    )


@app.post("/classification/predict")
def classify_complaint(
    request: ClassificationRequest,
):
    try:
        return classifier.predict(
            request.text
        )

    except ValueError as exception:
        raise HTTPException(
            status_code=422,
            detail=str(exception),
        ) from exception


@app.post("/complaints/analyze")
def analyze_complaint(
    request: ComplaintAnalysisRequest,
):
    try:
        return complaint_analyzer.analyze(
            request.description
        )

    except ValueError as exception:
        raise HTTPException(
            status_code=422,
            detail=str(exception),
        ) from exception


@app.post("/complaints/similar")
def detect_similar_complaints(
    request: SimilarComplaintRequest,
):
    try:
        return (
            similar_complaint_detector.detect(
                complaint=(
                    request
                    .complaint
                    .model_dump()
                ),
                candidates=[
                    candidate.model_dump()
                    for candidate
                    in request.candidates
                ],
                limit=request.limit,
            )
        )

    except ValueError as exception:
        raise HTTPException(
            status_code=422,
            detail=str(exception),
        ) from exception


@app.post(
    "/assignments/recommend-plumbers"
)
def recommend_plumbers(
    request: PlumberRecommendationRequest,
):
    try:
        return (
            plumber_assignment_recommender
            .recommend(
                complaint=(
                    request
                    .complaint
                    .model_dump()
                ),
                plumbers=[
                    plumber.model_dump()
                    for plumber
                    in request.plumbers
                ],
                limit=request.limit,
            )
        )

    except ValueError as exception:
        raise HTTPException(
            status_code=422,
            detail=str(exception),
        ) from exception
