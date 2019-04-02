<section class="accordion-section <?= empty($events) ? 'accordion-collapsed' : '' ?>">
    <div class="accordion-title">
        <h3><a href="#" class="fa accordion-toggle"></a> <?= t('Last activities') ?></h3>
    </div>
    <div class="accordion-content">
        <?= $this->render('event/events', ['events' => $events]) ?>
    </div>
</section>