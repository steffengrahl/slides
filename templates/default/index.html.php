<main>
    <section>
        <header>
            <h2>
                All your presentations
            </h2>
        </header>
        <?php if (!empty($presentations)) : ?>
        <ul>
            <?php foreach ($presentations as $presentation) : ?>
            <li>
                <a href="?presentation=<?= urlencode($presentation->getTitle()) ?>"><?= $presentation->getTitle() ?></a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else :  ?>
        <p>
            Didn't find any presentations. Let´s add one. 🙂
        </p>
        <?php endif; ?>
    </section>
</main>