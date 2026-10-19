<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Improving Alpine.js Code With Design Patterns - Jisse Reitsma</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&family=JetBrains+Mono:wght@400;600&display=swap">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/reveal.js@5/dist/reset.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/reveal.js@5/dist/reveal.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/reveal.js@5/dist/theme/black.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/highlight.js@11/styles/tokyo-night-dark.min.css">
    <link rel="stylesheet" href="theme.css">
</head>
<body>
<div class="reveal">
    <div class="slides">
        <section data-markdown data-separator="^---$" data-separator-notes="^Note:">
            <textarea data-template>
<?= htmlspecialchars(file_get_contents(__DIR__ . '/slides.md')) ?>
            </textarea>
        </section>
    </div>
    <div class="deck-footer">
        <span class="deck-footer__title">Improving Alpine.js Code With Design Patterns</span>
        <span class="deck-footer__sep">·</span>
        <span>Jisse Reitsma</span>
        <span class="deck-footer__sep">·</span>
        <span>Meet Magento DE 2026</span>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/reveal.js@5/dist/reveal.js"></script>
<script src="https://cdn.jsdelivr.net/npm/reveal.js@5/plugin/markdown/markdown.js"></script>
<script src="https://cdn.jsdelivr.net/npm/reveal.js@5/plugin/highlight/highlight.js"></script>
<script src="https://cdn.jsdelivr.net/npm/reveal.js@5/plugin/notes/notes.js"></script>
<script>
    Reveal.initialize({
        width: 1280,
        height: 720,
        margin: 0.04,
        center: false,
        hash: true,
        slideNumber: 'c/t',
        transition: 'fade',
        transitionSpeed: 'fast',
        backgroundTransition: 'fade',
        plugins: [RevealMarkdown, RevealHighlight, RevealNotes]
    });
</script>
</body>
</html>
