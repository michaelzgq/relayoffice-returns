#!/usr/bin/env python3
from __future__ import annotations

from pathlib import Path

from reportlab.lib import colors
from reportlab.lib.pagesizes import letter
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import inch
from reportlab.platypus import (
    Flowable,
    Frame,
    KeepTogether,
    PageBreak,
    Paragraph,
    SimpleDocTemplate,
    Spacer,
    Table,
    TableStyle,
)


ROOT = Path(__file__).resolve().parents[1]
OUTPUT_PATH = ROOT / "output/pdf/dossentry-3pl-return-exception-checklist-2026-04.pdf"
PUBLIC_PATH = ROOT / "web-panel/public/assets/dossentry/dossentry-3pl-return-exception-checklist-2026-04.pdf"

BLUE = colors.HexColor("#2563EB")
BLUE_DARK = colors.HexColor("#1D4ED8")
INK = colors.HexColor("#0F172A")
MUTED = colors.HexColor("#64748B")
LINE = colors.HexColor("#D8E1EB")
SOFT_BLUE = colors.HexColor("#EFF6FF")
SOFT_AMBER = colors.HexColor("#FFF7ED")
SOFT_GRAY = colors.HexColor("#F8FAFC")
GREEN = colors.HexColor("#15803D")
RED = colors.HexColor("#B45309")


styles = getSampleStyleSheet()
styles.add(
    ParagraphStyle(
        name="CoverTitle",
        parent=styles["Title"],
        fontName="Helvetica-Bold",
        fontSize=32,
        leading=35,
        textColor=INK,
        spaceAfter=14,
    )
)
styles.add(
    ParagraphStyle(
        name="SectionTitle",
        parent=styles["Heading1"],
        fontName="Helvetica-Bold",
        fontSize=20,
        leading=24,
        textColor=INK,
        spaceAfter=10,
    )
)
styles.add(
    ParagraphStyle(
        name="CardTitle",
        parent=styles["Heading3"],
        fontName="Helvetica-Bold",
        fontSize=12.5,
        leading=15,
        textColor=INK,
        spaceAfter=4,
    )
)
styles.add(
    ParagraphStyle(
        name="Body",
        parent=styles["BodyText"],
        fontName="Helvetica",
        fontSize=9.8,
        leading=14,
        textColor=colors.HexColor("#334155"),
        spaceAfter=7,
    )
)
styles.add(
    ParagraphStyle(
        name="Small",
        parent=styles["BodyText"],
        fontName="Helvetica",
        fontSize=8.5,
        leading=11.5,
        textColor=MUTED,
    )
)
styles.add(
    ParagraphStyle(
        name="Eyebrow",
        parent=styles["BodyText"],
        fontName="Helvetica-Bold",
        fontSize=8.5,
        leading=10,
        textColor=BLUE,
        uppercase=True,
        spaceAfter=7,
    )
)
styles.add(
    ParagraphStyle(
        name="Checklist",
        parent=styles["BodyText"],
        fontName="Helvetica",
        fontSize=9.4,
        leading=13.2,
        textColor=colors.HexColor("#334155"),
        leftIndent=10,
        firstLineIndent=-10,
        spaceAfter=5,
    )
)
styles.add(
    ParagraphStyle(
        name="Quote",
        parent=styles["BodyText"],
        fontName="Helvetica-Bold",
        fontSize=11,
        leading=16,
        textColor=INK,
    )
)


class Rule(Flowable):
    def __init__(self, color=LINE, thickness=1):
        super().__init__()
        self.color = color
        self.thickness = thickness
        self.height = 8

    def wrap(self, avail_width, avail_height):
        self.width = avail_width
        return avail_width, self.height

    def draw(self):
        self.canv.setStrokeColor(self.color)
        self.canv.setLineWidth(self.thickness)
        self.canv.line(0, self.height / 2, self.width, self.height / 2)


def p(text: str, style_name: str = "Body") -> Paragraph:
    return Paragraph(text, styles[style_name])


def bullet(text: str) -> Paragraph:
    return p(f"- {text}", "Checklist")


def card(title: str, body: str, bg=SOFT_GRAY) -> Table:
    data = [[p(title, "CardTitle")], [p(body, "Body")]]
    table = Table(data, colWidths=[2.35 * inch], hAlign="LEFT")
    table.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, -1), bg),
                ("BOX", (0, 0), (-1, -1), 0.75, LINE),
                ("ROUNDEDCORNERS", (0, 0), (-1, -1), 10),
                ("LEFTPADDING", (0, 0), (-1, -1), 12),
                ("RIGHTPADDING", (0, 0), (-1, -1), 12),
                ("TOPPADDING", (0, 0), (-1, -1), 10),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 10),
            ]
        )
    )
    return table


def section_header(kicker: str, title: str, intro: str | None = None):
    out = [p(kicker.upper(), "Eyebrow"), p(title, "SectionTitle")]
    if intro:
        out.append(p(intro, "Body"))
    return out


def header_footer(canvas, doc):
    canvas.saveState()
    width, height = letter
    canvas.setFillColor(BLUE)
    canvas.roundRect(0.55 * inch, height - 0.52 * inch, 0.26 * inch, 0.26 * inch, 5, fill=1, stroke=0)
    canvas.setFillColor(colors.white)
    canvas.setFont("Helvetica-Bold", 9)
    canvas.drawCentredString(0.68 * inch, height - 0.43 * inch, "D")
    canvas.setFillColor(INK)
    canvas.setFont("Helvetica-Bold", 9.5)
    canvas.drawString(0.88 * inch, height - 0.43 * inch, "Dossentry")
    canvas.setFillColor(MUTED)
    canvas.setFont("Helvetica", 8)
    canvas.drawRightString(width - 0.55 * inch, 0.38 * inch, f"Page {doc.page}")
    canvas.drawString(0.55 * inch, 0.38 * inch, "3PL Return Exception Checklist")
    canvas.restoreState()


def cover_story() -> list:
    return [
        Spacer(1, 0.26 * inch),
        p("3PL RETURN EXCEPTION CHECKLIST", "Eyebrow"),
        p("Catch the cases that should stay on hold before refund release.", "CoverTitle"),
        p(
            "Use this checklist when a return looks risky, incomplete, mismatched, or likely to be questioned by a brand later. "
            "It is built for warehouse-side exception control, not generic inspections.",
            "Body",
        ),
        Spacer(1, 0.10 * inch),
        Table(
            [
                [
                    card(
                        "Best fit",
                        "Multi-brand 3PLs, returns centers, and ecommerce operators handling disputed returns, SKU mismatches, missing items, damaged goods, or unclear refund release decisions.",
                        SOFT_BLUE,
                    ),
                    card(
                        "Fast audit offer",
                        "Send 3 anonymized return cases or one return SOP. Dossentry will show where refund leakage, SKU mismatch, missing-item risk, and brand-rule gaps are happening.",
                        SOFT_AMBER,
                    ),
                ]
            ],
            colWidths=[2.55 * inch, 2.55 * inch],
            hAlign="LEFT",
        ),
        Spacer(1, 0.22 * inch),
        p("Use this before a refund is released when:", "CardTitle"),
        bullet("The expected SKU, serial, carton label, or item identity does not match what arrived."),
        bullet("Required photos or notes are missing, unclear, or scattered across folders and chat."),
        bullet("The brand rule is unclear, lives in a PDF, or depends on one experienced employee's memory."),
        bullet("The case may need brand, ops, client success, or finance review later."),
        Spacer(1, 0.18 * inch),
        p("Audit page: https://dossentry.com/return-exception-audit", "Quote"),
        p("Sample case: https://dossentry.com/sample-cases/serial-mismatch-review", "Small"),
    ]


def checklist_page() -> list:
    items = [
        ("1. Expected record", "Do we have the expected RMA, SKU, serial, tracking, brand, and return reason before making a decision?"),
        ("2. Item identity", "Does the observed item, carton label, SKU, serial, and product family match the expected return record?"),
        ("3. Missing item / parts", "Are all components, accessories, manuals, and required packaging present based on the brand rule?"),
        ("4. Damage clarity", "Is damage documented with enough angles, context, and severity language for a reviewer to understand it later?"),
        ("5. Evidence completeness", "Are required photos, notes, timestamps, and inspector details attached before the case moves forward?"),
        ("6. Brand rule", "Which exact brand rule applies, and is it visible to the operator at the moment of inspection?"),
        ("7. Refund hold trigger", "Should this case stay on hold because identity, condition, or evidence is incomplete or contradictory?"),
        ("8. Reviewer note", "Can the next reviewer understand what happened in under one minute without asking the inspector?"),
        ("9. Escalation path", "Who decides the next action: warehouse lead, client success, brand reviewer, or finance?"),
        ("10. Final record", "Can this case be shared as one review-ready link or PDF instead of screenshots and chat history?"),
    ]
    rows = []
    for idx, (title, body) in enumerate(items):
        rows.append(
            [
                p(title, "CardTitle"),
                p(body, "Body"),
                p("Pass / Gap / Hold", "Small"),
            ]
        )
    table = Table(rows, colWidths=[1.45 * inch, 3.55 * inch, 1.15 * inch], repeatRows=0)
    table.setStyle(
        TableStyle(
            [
                ("GRID", (0, 0), (-1, -1), 0.45, LINE),
                ("BACKGROUND", (0, 0), (-1, -1), colors.white),
                ("VALIGN", (0, 0), (-1, -1), "TOP"),
                ("LEFTPADDING", (0, 0), (-1, -1), 8),
                ("RIGHTPADDING", (0, 0), (-1, -1), 8),
                ("TOPPADDING", (0, 0), (-1, -1), 8),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 8),
            ]
        )
    )
    return [
        *section_header(
            "Operator checklist",
            "10 checks before refund release",
            "If any high-risk item below is a gap, the safer workflow is to hold the case and create a review-ready record.",
        ),
        Spacer(1, 0.12 * inch),
        table,
    ]


def rule_engine_page() -> list:
    return [
        *section_header(
            "Brand rule control",
            "The moat is not the photo checklist. It is applying the correct brand rule.",
            "Most 3PL friction comes from rules scattered across SOP PDFs, Slack messages, onboarding notes, and operator memory.",
        ),
        Spacer(1, 0.08 * inch),
        Table(
            [
                [
                    card("Rule location", "Where does the rule live today: WMS note, SOP PDF, Slack, spreadsheet, or someone's memory?", SOFT_BLUE),
                    card("Rule owner", "Who can change the rule, and how does the warehouse know the latest version is active?", SOFT_GRAY),
                ],
                [
                    card("Rule trigger", "Which SKU, brand, condition, missing part, or serial mismatch should automatically trigger review?", SOFT_GRAY),
                    card("Rule output", "Does the rule tell the operator to restock, hold, repair, dispose, escalate, or request brand review?", SOFT_BLUE),
                ],
            ],
            colWidths=[2.55 * inch, 2.55 * inch],
            hAlign="LEFT",
        ),
        Spacer(1, 0.18 * inch),
        p("Minimum brand rule fields to capture", "CardTitle"),
        bullet("Brand name and product scope."),
        bullet("Required evidence slots: carton, label, item, damage, missing accessories, serial, packaging."),
        bullet("Auto-hold triggers: SKU mismatch, serial mismatch, missing item, photo gap, condition conflict."),
        bullet("Reviewer note template: what the next person must verify before release."),
        bullet("Rule version and effective date."),
        Spacer(1, 0.18 * inch),
        p("Decision rule", "CardTitle"),
        p(
            "If a brand rule is not visible at the inspection station, the process is still dependent on memory. "
            "That is where Dossentry should replace tribal knowledge with structured exception control.",
            "Body",
        ),
    ]


def evidence_page() -> list:
    return [
        *section_header(
            "Evidence pack",
            "What a reviewer needs when the case is challenged later",
            "The goal is not more photos. The goal is a defensible record that connects expected data, observed facts, and the hold decision.",
        ),
        Spacer(1, 0.08 * inch),
        Table(
            [
                [
                    card("Expected inbound", "RMA, SKU, serial, tracking, brand, return reason, expected condition.", SOFT_BLUE),
                    card("Observed facts", "What arrived, what labels show, what is missing, what is damaged, what was opened.", SOFT_GRAY),
                ],
                [
                    card("Evidence set", "Required photos, notes, timestamps, inspector identity, evidence completeness status.", SOFT_GRAY),
                    card("Review posture", "Hold reason, recommended action, reviewer note, escalation owner, final decision status.", SOFT_BLUE),
                ],
            ],
            colWidths=[2.55 * inch, 2.55 * inch],
            hAlign="LEFT",
        ),
        Spacer(1, 0.20 * inch),
        p("Email template for a 3PL prospect", "CardTitle"),
        Table(
            [
                [
                    p(
                        "Hi [Name], I noticed your team handles ecommerce returns and likely has some cases where SKU, serial, damage, or missing-item details affect refund release. "
                        "Dossentry helps 3PL teams catch those exceptions before refunds move forward. If you send 3 anonymized return cases or one SOP, I can show where the current workflow creates refund leakage, evidence gaps, or brand-rule risk. No WMS integration is needed for the audit.",
                        "Body",
                    )
                ]
            ],
            colWidths=[5.45 * inch],
            style=[
                ("BACKGROUND", (0, 0), (-1, -1), SOFT_AMBER),
                ("BOX", (0, 0), (-1, -1), 0.75, LINE),
                ("LEFTPADDING", (0, 0), (-1, -1), 14),
                ("RIGHTPADDING", (0, 0), (-1, -1), 14),
                ("TOPPADDING", (0, 0), (-1, -1), 12),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 12),
            ],
        ),
        Spacer(1, 0.18 * inch),
        p("Next action", "CardTitle"),
        p("Use the audit page as the CTA: https://dossentry.com/return-exception-audit", "Quote"),
    ]


def build_pdf(path: Path):
    path.parent.mkdir(parents=True, exist_ok=True)
    doc = SimpleDocTemplate(
        str(path),
        pagesize=letter,
        leftMargin=0.6 * inch,
        rightMargin=0.6 * inch,
        topMargin=0.72 * inch,
        bottomMargin=0.62 * inch,
        title="Dossentry 3PL Return Exception Checklist",
        author="Dossentry",
    )

    story = []
    story.extend(cover_story())
    story.append(PageBreak())
    story.extend(checklist_page())
    story.append(PageBreak())
    story.extend(rule_engine_page())
    story.append(PageBreak())
    story.extend(evidence_page())

    doc.build(story, onFirstPage=header_footer, onLaterPages=header_footer)


def main():
    build_pdf(OUTPUT_PATH)
    PUBLIC_PATH.parent.mkdir(parents=True, exist_ok=True)
    PUBLIC_PATH.write_bytes(OUTPUT_PATH.read_bytes())
    print(OUTPUT_PATH)
    print(PUBLIC_PATH)


if __name__ == "__main__":
    main()
