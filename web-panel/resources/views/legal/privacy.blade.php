@extends('legal.layout')

@section('title', 'Privacy Policy')

@section('content')
    <span class="eyebrow">Privacy Policy</span>
    <h1>Privacy Policy</h1>
    <p class="lede">
        This policy explains what Dossentry collects, how it is used, and how hosted evaluation data differs from formal
        customer deployments. This page applies to the public website, the workflow review form, and the hosted demo.
    </p>
    <div class="meta">Effective date: April 11, 2026</div>

    <section class="section">
        <h2>1. Who operates Dossentry</h2>
        <p>
            Dossentry is a trade name used for return-evidence and decision-workflow software. Questions about this policy can be
            sent to <strong>michael.zgq@gmail.com</strong>.
        </p>
    </section>

    <section class="section">
        <h2>2. Information we collect</h2>
        <p>We collect information needed to operate the website, hosted demo, and workflow review intake.</p>
        <ul>
            <li>full name</li>
            <li>work email address</li>
            <li>company name</li>
            <li>role title</li>
            <li>monthly return volume selection</li>
            <li>workflow notes submitted through the review request form</li>
            <li>demo sign-in activity and case interaction within the hosted evaluation workspace</li>
        </ul>
    </section>

    <section class="section">
        <h2>3. How we use information</h2>
        <ul>
            <li>respond to workflow review requests</li>
            <li>operate the hosted demo and guest workspace</li>
            <li>support product evaluation and follow-up conversations</li>
            <li>improve onboarding, product UX, and support materials</li>
            <li>protect the service from misuse, spam, or abusive behavior</li>
        </ul>
    </section>

    <section class="section">
        <h2>4. Hosted demo vs customer deployments</h2>
        <p>
            Dossentry offers a hosted evaluation demo and may also be delivered as a self-hosted customer deployment.
            Hosted demo data is not the same as a formal customer production environment.
        </p>
        <p>
            In self-hosted production deployments, customer case data, uploaded evidence, and staff accounts may remain in
            customer-controlled infrastructure rather than on Dossentry-operated systems.
        </p>
        <div class="note">
            Dossentry's public demo is a shared evaluation workspace. It may be reset, refreshed, or replaced at any time and
            should not be used for production data.
        </div>
    </section>

    <section class="section">
        <h2>5. Data ownership</h2>
        <p>
            For customer production deployments, Dossentry is designed around customer-owned operational data. Uploaded photos,
            case records, and staff access records can be stored in the customer's own environment when deployed in self-hosted mode.
        </p>
    </section>

    <section class="section">
        <h2>6. Sharing of information</h2>
        <p>We do not sell submitted workflow-review information. Information may be shared only as needed for:</p>
        <ul>
            <li>service providers that support hosting, email delivery, or technical operations</li>
            <li>legal compliance, fraud prevention, or security response</li>
            <li>professional advisors where reasonably necessary to operate the business</li>
        </ul>
    </section>

    <section class="section">
        <h2>7. Security</h2>
        <p>
            We use reasonable administrative and technical safeguards for the hosted website and demo. No internet-connected service
            can promise absolute security, so Dossentry does not guarantee that unauthorized access will never occur.
        </p>
    </section>

    <section class="section">
        <h2>8. Retention</h2>
        <p>
            Workflow review submissions and demo-related records may be retained for product evaluation, support, and business
            recordkeeping. Shared demo data may also be deleted or reset without notice.
        </p>
    </section>

    <section class="section">
        <h2>9. AI and optional knowledge workspace</h2>
        <p>
            Dossentry may offer an optional Pro local knowledge workspace. Where that add-on is deployed in customer infrastructure,
            customers may use customer-owned AI API keys. Dossentry does not claim ownership of customer-owned prompts or customer-side
            knowledge base content stored in that customer environment.
        </p>
    </section>

    <section class="section">
        <h2>10. Policy updates</h2>
        <p>
            This policy may be updated from time to time. The effective date at the top of this page will be updated when material
            changes are published.
        </p>
    </section>
@endsection
