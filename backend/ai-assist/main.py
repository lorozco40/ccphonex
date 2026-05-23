from datetime import datetime, timezone
from typing import Any

from fastapi import FastAPI
from pydantic import BaseModel, Field


app = FastAPI(title="ccphonex-ai-assist", version="0.1.0")


class Message(BaseModel):
    speaker: str = Field(default="agent")
    text: str


class RecommendationRequest(BaseModel):
    channel: str = Field(default="chat")
    conversation: list[Message] = Field(default_factory=list)
    metadata: dict[str, Any] = Field(default_factory=dict)


class SummaryRequest(BaseModel):
    channel: str = Field(default="chat")
    text: str
    metadata: dict[str, Any] = Field(default_factory=dict)


def _clean_text(value: str) -> str:
    return " ".join((value or "").strip().split())


def _shorten(value: str, size: int = 180) -> str:
    clean = _clean_text(value)
    if len(clean) <= size:
        return clean
    return f"{clean[: size - 1]}…"


def _build_replies(latest_text: str) -> list[str]:
    text = latest_text.lower()
    if any(word in text for word in ["precio", "costo", "tarifa"]):
        return [
            "Gracias por contactarnos. Te comparto opciones y precios según tu necesidad.",
            "¿Te puedo confirmar primero el plan o servicio que buscas para darte el costo exacto?",
        ]
    if any(word in text for word in ["cancel", "baja", "molesto", "queja"]):
        return [
            "Lamento la situación. Te ayudo de inmediato a resolverlo paso a paso.",
            "Para atender tu solicitud sin demoras, ¿me compartes tu número de cliente o ticket?",
        ]
    if any(word in text for word in ["hola", "buenos", "buenas", "ayuda"]):
        return [
            "Hola, con gusto te apoyo. ¿Me indicas tu caso para revisarlo?",
            "Gracias por escribirnos. Estoy validando la mejor solución para ti.",
        ]
    return [
        "Gracias por la información. Estoy revisando tu caso para darte una respuesta precisa.",
        "Para avanzar más rápido, ¿puedes confirmar tu número de contacto y el detalle principal?",
    ]


@app.get("/health")
def health() -> dict[str, str]:
    return {"status": "ok"}


@app.post("/v1/summarize")
def summarize(req: SummaryRequest) -> dict[str, Any]:
    summary = _shorten(req.text, 220)
    category = "general"
    lower = req.text.lower()
    if any(word in lower for word in ["pago", "factura", "cobro"]):
        category = "billing"
    elif any(word in lower for word in ["falla", "error", "caido", "no funciona"]):
        category = "technical"
    elif any(word in lower for word in ["cancel", "baja"]):
        category = "retention"

    return {
        "summary": summary,
        "category": category,
        "confidence": 0.55,
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "shadow_mode": True,
    }


@app.post("/v1/recommendations")
def recommendations(req: RecommendationRequest) -> dict[str, Any]:
    latest_text = ""
    if req.conversation:
        latest_text = req.conversation[-1].text

    return {
        "suggested_replies": _build_replies(latest_text),
        "next_best_action": "confirm_customer_context",
        "conversation_summary": _shorten(latest_text, 200),
        "confidence": 0.52,
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "shadow_mode": True,
    }
