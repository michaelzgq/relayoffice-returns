from __future__ import annotations

from pathlib import Path

from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_LEFT
from reportlab.lib.pagesizes import LETTER
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import inch
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.platypus import (
    BaseDocTemplate,
    Frame,
    Image,
    PageBreak,
    PageTemplate,
    Paragraph,
    Spacer,
    Table,
    TableStyle,
)


ROOT = Path("/Users/mikezhang/Desktop/projects/6POS")
ASSET_DIR = ROOT / "web-panel" / "public" / "assets" / "dossentry"
HERO_IMAGE_PATH = ASSET_DIR / "hero-bg.png"
OUTPUT_DIR = ROOT / "output" / "pdf"

BRAND_NAVY = colors.HexColor("#10233b")
BRAND_BLUE = colors.HexColor("#2563eb")
BRAND_ORANGE = colors.HexColor("#f97316")
BRAND_PALE_BLUE = colors.HexColor("#eef4ff")
BRAND_SOFT_BLUE = colors.HexColor("#f8fbff")
BRAND_TEXT = colors.HexColor("#243447")
BRAND_MUTED = colors.HexColor("#5f6f82")
BRAND_LINE = colors.HexColor("#d8e0ea")
BRAND_WARM = colors.HexColor("#fff8f0")

CHINESE_FONT_CANDIDATES = [
    Path("/Library/Fonts/Arial Unicode.ttf"),
    Path("/System/Library/Fonts/Supplemental/Arial Unicode.ttf"),
]


PACKS = {
    "en": {
        "output": OUTPUT_DIR / "dossentry-current-product-overview-2026-04.pdf",
        "footer_title": "Dossentry - Customer Product Overview",
        "page_label": "Page",
        "table_headers": ("Capability", "What It Does", "Why It Matters"),
        "eyebrow": "Brand-ready return evidence",
        "headline": "Defensible return evidence, generated on the warehouse floor.",
        "subtitle": (
            "Customer Edition - Updated April 13, 2026. Built for multi-brand warehouse teams "
            "that need client playbooks, close-up proof, and one clean review record."
        ),
        "intro": (
            "Dossentry is a warehouse-side exception-control layer for returns. "
            "It helps warehouse teams inspect on phones, apply the right brand rules, "
            "review cases in one queue, and send a Brand Review Link instead of rebuilding the story from screenshots and chat threads."
        ),
        "proof_pills": [
            "Phone-first evidence capture",
            "Brand Review Link",
            "No station rebuild",
            "Customer-owned deployment",
        ],
        "cover_highlights": [
            (
                "Client playbooks",
                "Turn each brand's SOP into a live inspection rule set with required proof and allowed actions.",
            ),
            (
                "Phone-first inspect",
                "Run scan, evidence capture, and inspection directly from a browser on devices the team already uses.",
            ),
            (
                "Decision Queue",
                "Keep disputed or high-risk cases in one operational queue with evidence readiness and audit context.",
            ),
            (
                "Brand defense pack",
                "Export a review-ready case pack or send one protected Brand Review Link when the brand asks questions.",
            ),
        ],
        "workflow_title": "Current End-To-End Workflow",
        "workflow_steps": [
            (
                "1",
                "Set playbooks",
                "Define brand-specific evidence rules, allowed conditions, required photos, and default decision state.",
            ),
            (
                "2",
                "Inspect on the floor",
                "Inspectors scan the label, capture close-up proof, and record condition, SKU, serial, and notes.",
            ),
            (
                "3",
                "Create the case record",
                "Every inspection becomes a structured case with evidence progress, timeline, and SLA age.",
            ),
            (
                "4",
                "Review in queue",
                "Ops filters cases by evidence gaps, age, or brand and moves them through hold, needs review, or ready for brand review.",
            ),
            (
                "5",
                "Share one clean record",
                "Send a Brand Review Link or PDF instead of stitching together folders, screenshots, and email context.",
            ),
        ],
        "capability_title": "What Teams Can Do Today",
        "capability_rows": [
            (
                "Client playbooks",
                "Create brand-specific rules for conditions, warehouse actions, required photos, SKU, serial, notes, and default decision state.",
                "Standardizes multi-brand handling without relying on memory or scattered SOPs.",
            ),
            (
                "Phone-first inspection",
                "Inspect from a browser with live scan, camera-photo scan fallback, hardware scan, or manual entry.",
                "Lets teams work on the floor without installing fixed stations.",
            ),
            (
                "Structured case record",
                "Create a case with evidence status, timeline, notes, SKU, serial, timestamps, and SLA age.",
                "Gives ops one record to review instead of reconstructing the case later.",
            ),
            (
                "Decision Queue",
                "Review cases by hold, needs review, or ready for brand review with evidence and aging filters.",
                "Keeps exception handling operational instead of ad hoc.",
            ),
            (
                "Brand Review Link",
                "Generate a protected read-only review record with photos, timeline, playbook snapshot, recommendation, and evidence status.",
                "Shortens brand back-and-forth when a return decision is challenged.",
            ),
            (
                "Brand defense pack PDF",
                "Open or download a case pack that combines summary, evidence, timeline, and decision context.",
                "Useful for escalations, client communication, and internal review.",
            ),
            (
                "Ops Board",
                "Track inspections today, missing evidence, backlog by brand, ready cases, and over-48-hour cases.",
                "Lets managers see throughput and risk without building a new report.",
            ),
            (
                "Customer-controlled deployment",
                "Support browser-based use, live demo access, and Docker self-hosted deployment.",
                "Fits operators who want workflow value without a heavy systems migration.",
            ),
        ],
        "reasons_title": "Why Warehouse Teams Use Dossentry",
        "reasons": [
            "Standardize multi-brand return handling without relying on PDFs, spreadsheets, or chat threads.",
            "Capture the close-up proof brands actually ask for, including serial labels, packaging damage, and inside-the-box evidence.",
            "Reduce back-and-forth when a brand questions how a return was handled.",
            "Block weak cases from moving forward before evidence is complete.",
            "Keep the current WMS and shopper-facing portal instead of replacing the full stack.",
            "Start on the devices the team already uses instead of rebuilding warehouse stations.",
        ],
        "fit_title": "Best Fit",
        "fit": (
            "Multi-brand 3PLs, operators serving multiple DTC brands, and warehouse teams that handle high-risk or disputed return cases."
        ),
        "not_fit_title": "Not Trying To Replace",
        "not_fit": (
            "A shopper-facing returns portal, a shipping label or exchange platform, a WMS replacement, or a full reverse logistics suite."
        ),
        "deployment_title": "Deployment And Access Model",
        "deployment_points": [
            "Browser-based warehouse workflow",
            "Live web demo entry point",
            "Docker self-hosted option",
            "Customer-controlled data and staff access",
        ],
        "links_title": "Current External Entry Points",
        "links": [
            ("Website", "https://dossentry.com"),
            ("Compare page", "https://dossentry.com/compare/generic-inspection-apps"),
            ("Demo login", "https://demo.dossentry.com/admin/auth/login"),
        ],
        "closing": (
            "Dossentry is strongest in the messy layer after the item is physically back in the warehouse: "
            "the point where the team must inspect it correctly, capture the right proof, justify the next action, "
            "and explain the case back to the brand if challenged."
        ),
    },
    "zh": {
        "output": OUTPUT_DIR / "dossentry-current-product-overview-zh-2026-04.pdf",
        "footer_title": "Dossentry - 客户版产品总览",
        "page_label": "第",
        "table_headers": ("当前能力", "它具体做什么", "为什么值得用"),
        "eyebrow": "可对品牌方解释的退货证据",
        "headline": "在仓库一线生成、站得住脚的退货证据。",
        "subtitle": (
            "客户版 - 更新于 2026年4月13日。适合多品牌仓库团队，"
            "需要品牌规则、近距离证据和可直接发给品牌方的整洁 case record。"
        ),
        "intro": (
            "Dossentry 是一个面向仓库端的退货异常处理层。"
            "它帮助仓库团队用手机完成检查、套用正确的品牌规则、在一个队列里复核 case，"
            "并在品牌方质疑时直接发出 Brand Review Link，而不是重新拼截图、文件夹和聊天记录。"
        ),
        "proof_pills": [
            "手机优先取证",
            "Brand Review Link",
            "无需重建工位",
            "客户自有部署",
        ],
        "cover_highlights": [
            (
                "品牌规则 Playbook",
                "把每个品牌的 SOP 变成实时检查规则，明确需要哪些证据、允许哪些处理动作。",
            ),
            (
                "手机端检查",
                "直接在浏览器里完成扫码、拍照、记录 condition 和必要字段，不要求固定拍摄工位。",
            ),
            (
                "Decision Queue",
                "把高风险或有争议的退货 case 放进统一复核队列，带证据完整度和审计上下文。",
            ),
            (
                "品牌复核资料",
                "需要对外解释时，可直接发送 Brand Review Link 或导出 PDF case pack。",
            ),
        ],
        "workflow_title": "当前完整流程",
        "workflow_steps": [
            (
                "1",
                "配置品牌规则",
                "为每个品牌定义允许的 condition、处理动作、必拍照片和默认决策状态。",
            ),
            (
                "2",
                "一线检查",
                "仓库员工在手机或浏览器里扫码、拍近景证据，并记录 SKU、序列号、备注和 condition。",
            ),
            (
                "3",
                "自动形成 case",
                "每次检查都会生成结构化 case，包含证据进度、timeline 和 SLA aging。",
            ),
            (
                "4",
                "队列复核",
                "运营人员按品牌、证据缺口、超时情况筛选 case，并推进到 hold、needs review 或 ready for brand review。",
            ),
            (
                "5",
                "对外发送整洁记录",
                "当品牌方质疑处理结果时，直接发送 Brand Review Link 或 PDF，而不是临时整理截图和文件夹。",
            ),
        ],
        "capability_title": "当前已经可用的能力",
        "capability_rows": [
            (
                "品牌规则 Playbook",
                "为不同品牌定义检查标准、允许的仓库动作、必拍照片、SKU、序列号、备注和默认决策状态。",
                "让一个仓库同时服务多个品牌时，不再依赖培训记忆和分散 SOP。",
            ),
            (
                "手机优先检查",
                "支持浏览器检查流程，包含 live scan、拍照识别兜底、硬件扫码枪输入和手动录入。",
                "让仓库团队直接在现有设备上工作，不需要重建固定工位。",
            ),
            (
                "结构化 case record",
                "自动记录证据状态、timeline、备注、SKU、序列号、时间戳和 SLA age。",
                "让运营可以直接复核，不用事后重新拼凑经过。",
            ),
            (
                "Decision Queue",
                "按 hold、needs review、ready for brand review 管理 case，并支持按证据缺口和超时筛选。",
                "把异常退货处理从零散沟通，变成可运营的流程。",
            ),
            (
                "Brand Review Link",
                "生成受保护的只读 case record，包含照片、timeline、playbook 快照、建议和证据状态。",
                "减少品牌方来回追问，提升对外解释效率。",
            ),
            (
                "Brand Defense Pack PDF",
                "导出包含摘要、证据、timeline 和决策上下文的 PDF case pack。",
                "适合 escalations、客户沟通和内部复盘。",
            ),
            (
                "Ops Board",
                "查看当日检查量、缺失证据、各品牌积压、ready cases 和超 48 小时 case。",
                "管理层不用单独做新报表，也能看到处理节奏和风险。",
            ),
            (
                "客户自有部署",
                "支持浏览器使用、在线 demo 和 Docker self-hosted 部署。",
                "适合想快速上手，但不想做重型系统迁移的团队。",
            ),
        ],
        "reasons_title": "客户为什么会用 Dossentry",
        "reasons": [
            "把多品牌退货处理标准化，不再依赖 PDF、Excel、共享文件夹或聊天记录。",
            "获取品牌方真正关心的近景证据，例如序列号标签、包装损伤、盒内状态和局部细节。",
            "当品牌方质疑退货处理结果时，减少来回解释成本。",
            "在证据不完整时阻止 case 继续流转。",
            "保留现有 WMS 和 shopper-facing returns portal，而不是整套替换。",
            "直接在团队已有设备上开始使用，而不是先重建仓库工位。",
        ],
        "fit_title": "最适合的客户",
        "fit": "多品牌 3PL、服务多个 DTC 品牌的运营团队，以及经常处理高风险或有争议退货 case 的仓库团队。",
        "not_fit_title": "它不是要替代什么",
        "not_fit": "它不是 shopper-facing returns portal，不是物流换货平台，不是 WMS，也不是完整 reverse logistics suite。",
        "deployment_title": "部署与访问方式",
        "deployment_points": [
            "浏览器即可使用的仓库工作流",
            "在线 demo 入口",
            "Docker self-hosted",
            "客户自有数据和员工账号控制",
        ],
        "links_title": "当前外部入口",
        "links": [
            ("官网", "https://dossentry.com"),
            ("对比页", "https://dossentry.com/compare/generic-inspection-apps"),
            ("Demo 登录", "https://demo.dossentry.com/admin/auth/login"),
        ],
        "closing": (
            "Dossentry 最强的场景，是退货已经回到仓库之后的那一段最混乱流程："
            "团队必须正确检查、拍对证据、说明下一步处理动作，并在品牌方追问时拿得出一份清晰 record。"
        ),
    },
}


def register_chinese_font() -> str:
    for candidate in CHINESE_FONT_CANDIDATES:
        if candidate.exists():
            pdfmetrics.registerFont(TTFont("ArialUnicode", str(candidate)))
            return "ArialUnicode"
    raise FileNotFoundError("Could not find a Chinese-capable font for PDF generation.")


CHINESE_FONT = register_chinese_font()


def build_styles(language: str):
    styles = getSampleStyleSheet()
    font_regular = "Helvetica" if language == "en" else CHINESE_FONT
    font_bold = "Helvetica-Bold" if language == "en" else CHINESE_FONT
    styles.add(
        ParagraphStyle(
            name="Eyebrow",
            parent=styles["BodyText"],
            fontName=font_bold,
            fontSize=9,
            leading=12,
            textColor=BRAND_BLUE,
            spaceAfter=6,
        )
    )
    styles.add(
        ParagraphStyle(
            name="HeroTitle",
            parent=styles["Title"],
            fontName=font_bold,
            fontSize=25,
            leading=30,
            textColor=BRAND_NAVY,
            alignment=TA_LEFT,
            spaceAfter=10,
        )
    )
    styles.add(
        ParagraphStyle(
            name="HeroSubtitle",
            parent=styles["BodyText"],
            fontName=font_regular,
            fontSize=11,
            leading=15,
            textColor=BRAND_MUTED,
            spaceAfter=10,
        )
    )
    styles.add(
        ParagraphStyle(
            name="Body",
            parent=styles["BodyText"],
            fontName=font_regular,
            fontSize=10.2,
            leading=14.2,
            textColor=BRAND_TEXT,
            spaceAfter=5,
        )
    )
    styles.add(
        ParagraphStyle(
            name="SectionTitle",
            parent=styles["Heading2"],
            fontName=font_bold,
            fontSize=15,
            leading=19,
            textColor=BRAND_NAVY,
            spaceBefore=6,
            spaceAfter=8,
        )
    )
    styles.add(
        ParagraphStyle(
            name="MiniTitle",
            parent=styles["Heading3"],
            fontName=font_bold,
            fontSize=11.5,
            leading=14,
            textColor=BRAND_NAVY,
            spaceAfter=4,
        )
    )
    styles.add(
        ParagraphStyle(
            name="CardTitle",
            parent=styles["BodyText"],
            fontName=font_bold,
            fontSize=11.2,
            leading=14,
            textColor=BRAND_NAVY,
            spaceAfter=4,
        )
    )
    styles.add(
        ParagraphStyle(
            name="CardBody",
            parent=styles["BodyText"],
            fontName=font_regular,
            fontSize=9.4,
            leading=12.8,
            textColor=BRAND_TEXT,
        )
    )
    styles.add(
        ParagraphStyle(
            name="StepNumber",
            parent=styles["BodyText"],
            fontName=font_bold,
            fontSize=8.5,
            leading=10,
            textColor=BRAND_BLUE,
            alignment=TA_CENTER,
        )
    )
    styles.add(
        ParagraphStyle(
            name="BulletBody",
            parent=styles["BodyText"],
            fontName=font_regular,
            fontSize=10.2,
            leading=14,
            leftIndent=14,
            firstLineIndent=-8,
            bulletIndent=0,
            textColor=BRAND_TEXT,
            spaceAfter=4,
        )
    )
    styles.add(
        ParagraphStyle(
            name="Chip",
            parent=styles["BodyText"],
            fontName=font_bold,
            fontSize=8.4,
            leading=10,
            textColor=BRAND_BLUE,
            alignment=TA_CENTER,
        )
    )
    styles.add(
        ParagraphStyle(
            name="Footer",
            parent=styles["BodyText"],
            fontName=font_regular,
            fontSize=8.2,
            leading=10,
            textColor=colors.HexColor("#6b7280"),
            alignment=TA_CENTER,
        )
    )
    return styles, font_regular, font_bold


def footer(canvas, doc):
    pack = PACKS[doc.language]
    _, font_regular, _ = build_styles(doc.language)
    canvas.saveState()
    canvas.setStrokeColor(BRAND_LINE)
    canvas.line(doc.leftMargin, 0.55 * inch, LETTER[0] - doc.rightMargin, 0.55 * inch)
    canvas.setFont(font_regular, 8.2)
    canvas.setFillColor(colors.HexColor("#6b7280"))
    canvas.drawString(doc.leftMargin, 0.35 * inch, pack["footer_title"])
    page_text = f"{pack['page_label']} {canvas.getPageNumber()}" if doc.language == "en" else f"{pack['page_label']} {canvas.getPageNumber()} 页"
    canvas.drawRightString(LETTER[0] - doc.rightMargin, 0.35 * inch, page_text)
    canvas.restoreState()


def paragraph(text: str, style):
    return Paragraph(text, style)


def bullet(text: str, styles):
    return Paragraph(f"&bull; {text}", styles["BulletBody"])


def chip(text: str, styles):
    pill = Table([[paragraph(text, styles["Chip"])]], colWidths=[1.48 * inch])
    pill.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, -1), BRAND_PALE_BLUE),
                ("BOX", (0, 0), (-1, -1), 0.6, colors.HexColor("#bfd4ff")),
                ("LEFTPADDING", (0, 0), (-1, -1), 7),
                ("RIGHTPADDING", (0, 0), (-1, -1), 7),
                ("TOPPADDING", (0, 0), (-1, -1), 5),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 5),
            ]
        )
    )
    return pill


def build_cover(pack, styles):
    hero_image = Image(str(HERO_IMAGE_PATH), width=2.45 * inch, height=3.2 * inch)
    hero_image.hAlign = "RIGHT"

    chip_row_1 = Table(
        [[chip(pack["proof_pills"][0], styles), chip(pack["proof_pills"][1], styles)]],
        colWidths=[1.58 * inch, 1.58 * inch],
    )
    chip_row_1.setStyle(TableStyle([("LEFTPADDING", (0, 0), (-1, -1), 0), ("RIGHTPADDING", (0, 0), (-1, -1), 0)]))
    chip_row_2 = Table(
        [[chip(pack["proof_pills"][2], styles), chip(pack["proof_pills"][3], styles)]],
        colWidths=[1.58 * inch, 1.58 * inch],
    )
    chip_row_2.setStyle(TableStyle([("LEFTPADDING", (0, 0), (-1, -1), 0), ("RIGHTPADDING", (0, 0), (-1, -1), 0)]))

    left_stack = [
        paragraph(pack["eyebrow"], styles["Eyebrow"]),
        paragraph(pack["headline"], styles["HeroTitle"]),
        paragraph(pack["subtitle"], styles["HeroSubtitle"]),
        paragraph(pack["intro"], styles["Body"]),
        Spacer(1, 0.1 * inch),
        chip_row_1,
        Spacer(1, 0.08 * inch),
        chip_row_2,
    ]

    cover = Table(
        [[left_stack, hero_image]],
        colWidths=[4.0 * inch, 2.2 * inch],
    )
    cover.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, -1), colors.white),
                ("VALIGN", (0, 0), (-1, -1), "TOP"),
                ("LEFTPADDING", (0, 0), (-1, -1), 0),
                ("RIGHTPADDING", (0, 0), (-1, -1), 0),
                ("TOPPADDING", (0, 0), (-1, -1), 0),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 0),
            ]
        )
    )
    return cover


def build_highlight_grid(pack, styles):
    cards = []
    for title, body in pack["cover_highlights"]:
        card = Table(
            [[paragraph(title, styles["CardTitle"])], [paragraph(body, styles["CardBody"])]],
            colWidths=[3.0 * inch],
        )
        card.setStyle(
            TableStyle(
                [
                    ("BACKGROUND", (0, 0), (-1, -1), BRAND_SOFT_BLUE),
                    ("BOX", (0, 0), (-1, -1), 0.7, BRAND_LINE),
                    ("LEFTPADDING", (0, 0), (-1, -1), 10),
                    ("RIGHTPADDING", (0, 0), (-1, -1), 10),
                    ("TOPPADDING", (0, 0), (-1, -1), 10),
                    ("BOTTOMPADDING", (0, 0), (-1, -1), 10),
                    ("VALIGN", (0, 0), (-1, -1), "TOP"),
                ]
            )
        )
        cards.append(card)

    row_1 = Table([cards[:2]], colWidths=[3.08 * inch, 3.08 * inch])
    row_2 = Table([cards[2:]], colWidths=[3.08 * inch, 3.08 * inch])
    row_1.setStyle(TableStyle([("VALIGN", (0, 0), (-1, -1), "TOP")]))
    row_2.setStyle(TableStyle([("VALIGN", (0, 0), (-1, -1), "TOP")]))
    return [row_1, Spacer(1, 0.14 * inch), row_2]


def build_workflow_cards(pack, styles):
    cards = []
    for number, title, body in pack["workflow_steps"]:
        card = Table(
            [
                [paragraph(number, styles["StepNumber"])],
                [paragraph(title, styles["CardTitle"])],
                [paragraph(body, styles["CardBody"])],
            ],
            colWidths=[1.9 * inch],
        )
        card.setStyle(
            TableStyle(
                [
                    ("BACKGROUND", (0, 0), (-1, -1), colors.white),
                    ("BOX", (0, 0), (-1, -1), 0.8, BRAND_LINE),
                    ("LINEABOVE", (0, 0), (-1, 0), 2.0, BRAND_ORANGE),
                    ("LEFTPADDING", (0, 0), (-1, -1), 10),
                    ("RIGHTPADDING", (0, 0), (-1, -1), 10),
                    ("TOPPADDING", (0, 0), (-1, -1), 8),
                    ("BOTTOMPADDING", (0, 0), (-1, -1), 8),
                    ("VALIGN", (0, 0), (-1, -1), "TOP"),
                ]
            )
        )
        cards.append(card)

    top = Table([cards[:3]], colWidths=[2.08 * inch, 2.08 * inch, 2.08 * inch])
    bottom = Table([cards[3:]], colWidths=[3.15 * inch, 3.15 * inch])
    top.setStyle(TableStyle([("VALIGN", (0, 0), (-1, -1), "TOP")]))
    bottom.setStyle(TableStyle([("VALIGN", (0, 0), (-1, -1), "TOP")]))
    return [top, Spacer(1, 0.14 * inch), bottom]


def build_capability_table(pack, styles, font_regular, font_bold):
    header_capability, header_action, header_outcome = pack["table_headers"]
    rows = [
        [
            paragraph(f"<b>{header_capability}</b>", styles["Body"]),
            paragraph(f"<b>{header_action}</b>", styles["Body"]),
            paragraph(f"<b>{header_outcome}</b>", styles["Body"]),
        ]
    ]
    for capability, action, outcome in pack["capability_rows"]:
        rows.append(
            [
                paragraph(capability, styles["Body"]),
                paragraph(action, styles["Body"]),
                paragraph(outcome, styles["Body"]),
            ]
        )
    table = Table(rows, colWidths=[1.4 * inch, 2.55 * inch, 2.55 * inch], repeatRows=1)
    table.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, 0), BRAND_PALE_BLUE),
                ("TEXTCOLOR", (0, 0), (-1, 0), BRAND_NAVY),
                ("FONTNAME", (0, 0), (-1, 0), font_bold),
                ("FONTNAME", (0, 1), (-1, -1), font_regular),
                ("FONTSIZE", (0, 0), (-1, -1), 9.1),
                ("LEADING", (0, 0), (-1, -1), 11.5),
                ("VALIGN", (0, 0), (-1, -1), "TOP"),
                ("GRID", (0, 0), (-1, -1), 0.55, BRAND_LINE),
                ("ROWBACKGROUNDS", (0, 1), (-1, -1), [colors.white, BRAND_SOFT_BLUE]),
                ("LEFTPADDING", (0, 0), (-1, -1), 8),
                ("RIGHTPADDING", (0, 0), (-1, -1), 8),
                ("TOPPADDING", (0, 0), (-1, -1), 7),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 7),
            ]
        )
    )
    return table


def build_fit_panel(pack, styles):
    table = Table(
        [
            [
                paragraph(pack["fit_title"], styles["MiniTitle"]),
                paragraph(pack["not_fit_title"], styles["MiniTitle"]),
            ],
            [
                paragraph(pack["fit"], styles["Body"]),
                paragraph(pack["not_fit"], styles["Body"]),
            ],
        ],
        colWidths=[3.12 * inch, 3.12 * inch],
    )
    table.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, 0), BRAND_PALE_BLUE),
                ("GRID", (0, 0), (-1, -1), 0.6, BRAND_LINE),
                ("LEFTPADDING", (0, 0), (-1, -1), 10),
                ("RIGHTPADDING", (0, 0), (-1, -1), 10),
                ("TOPPADDING", (0, 0), (-1, -1), 8),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 8),
                ("VALIGN", (0, 0), (-1, -1), "TOP"),
            ]
        )
    )
    return table


def build_links(pack, styles):
    links = []
    for label, href in pack["links"]:
        links.append(Paragraph(f'&bull; <b>{label}:</b> <link href="{href}" color="blue">{href}</link>', styles["Body"]))
    return links


def build_story(language: str):
    pack = PACKS[language]
    styles, font_regular, font_bold = build_styles(language)
    story = []

    story.append(Spacer(1, 0.2 * inch))
    story.append(build_cover(pack, styles))
    story.append(Spacer(1, 0.24 * inch))
    story.extend(build_highlight_grid(pack, styles))

    warm_callout = Table(
        [[paragraph(pack["closing"], styles["Body"])]],
        colWidths=[6.4 * inch],
    )
    warm_callout.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, -1), BRAND_WARM),
                ("BOX", (0, 0), (-1, -1), 0.8, colors.HexColor("#f0c48a")),
                ("LEFTPADDING", (0, 0), (-1, -1), 12),
                ("RIGHTPADDING", (0, 0), (-1, -1), 12),
                ("TOPPADDING", (0, 0), (-1, -1), 10),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 10),
            ]
        )
    )
    story.append(Spacer(1, 0.2 * inch))
    story.append(warm_callout)

    story.append(PageBreak())
    story.append(paragraph(pack["workflow_title"], styles["SectionTitle"]))
    story.extend(build_workflow_cards(pack, styles))

    story.append(Spacer(1, 0.22 * inch))
    story.append(paragraph(pack["reasons_title"], styles["SectionTitle"]))
    for item in pack["reasons"]:
        story.append(bullet(item, styles))

    story.append(PageBreak())
    story.append(paragraph(pack["capability_title"], styles["SectionTitle"]))
    story.append(
        paragraph(
            "The current product is already organized around warehouse exception handling rather than a generic inspection checklist.",
            styles["Body"],
        )
        if language == "en"
        else paragraph(
            "当前产品已经是围绕仓库异常退货处理设计，而不是一个泛化的 inspection checklist。",
            styles["Body"],
        )
    )
    story.append(build_capability_table(pack, styles, font_regular, font_bold))

    story.append(PageBreak())
    story.append(paragraph(pack["deployment_title"], styles["SectionTitle"]))
    for item in pack["deployment_points"]:
        story.append(bullet(item, styles))

    story.append(Spacer(1, 0.18 * inch))
    story.append(build_fit_panel(pack, styles))

    story.append(Spacer(1, 0.18 * inch))
    story.append(paragraph(pack["links_title"], styles["SectionTitle"]))
    story.extend(build_links(pack, styles))

    final_panel = Table(
        [[paragraph(pack["closing"], styles["Body"])]],
        colWidths=[6.4 * inch],
    )
    final_panel.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, -1), BRAND_PALE_BLUE),
                ("BOX", (0, 0), (-1, -1), 0.8, colors.HexColor("#93c5fd")),
                ("LEFTPADDING", (0, 0), (-1, -1), 12),
                ("RIGHTPADDING", (0, 0), (-1, -1), 12),
                ("TOPPADDING", (0, 0), (-1, -1), 10),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 10),
            ]
        )
    )
    story.append(Spacer(1, 0.2 * inch))
    story.append(final_panel)
    return story


def build_pdf(language: str):
    pack = PACKS[language]
    OUTPUT_DIR.mkdir(parents=True, exist_ok=True)

    doc = BaseDocTemplate(
        str(pack["output"]),
        pagesize=LETTER,
        leftMargin=0.7 * inch,
        rightMargin=0.7 * inch,
        topMargin=0.7 * inch,
        bottomMargin=0.8 * inch,
        title=pack["footer_title"],
        author="OpenAI Codex",
    )
    doc.language = language

    frame = Frame(doc.leftMargin, doc.bottomMargin, doc.width, doc.height, id="main")
    template = PageTemplate(id="main", frames=[frame], onPage=footer)
    doc.addPageTemplates([template])
    doc.build(build_story(language))


def main():
    for language in ("en", "zh"):
        build_pdf(language)


if __name__ == "__main__":
    main()
