<div class="toolbar tabbar tabbar-labels">
    <div class="toolbar-inner">
    <?php if(ModulesChecker::isEnabled("store")): ?>
        <a data-module="store" href="#view-stores" class="ttab tab-link <?=$active_tab=="store"?"tab-link-active":""?>">
            <i class="mdi mdi-storefront-outline"></i>
            <span class="tabbar-label"><?=_lang("Stores")?></span>
        </a>
    <?php endif; ?>
    <?php if(ModulesChecker::isEnabled("offer")): ?>
        <a data-module="offer" href="#view-offers" class="ttab  tab-link <?=$active_tab=="offer"?"tab-link-active":""?>" >
            <i class="mdi mdi-percent-outline"></i>
            <span class="tabbar-label"><?=_lang("Offers")?></span>
        </a>
    <?php endif; ?>
    <?php if(ModulesChecker::isEnabled("event")): ?>
        <a data-module="event" href="#view-events" class="ttab tab-link <?=$active_tab=="event"?"tab-link-active":""?>" >
            <i class="mdi mdi-calendar-outline"></i>
            <span class="tabbar-label"><?=_lang("Events")?></span>
        </a>
    <?php endif; ?>


    <?php if(ModulesChecker::isEnabled("booking") && GroupAccess::isGranted("booking")): ?>
            <a data-module="event" href="#view-booking" class="ttab tab-link <?=$active_tab=="booking"?"tab-link-active":""?>" >
                <i class="mdi  mdi-calendar-clock"></i>
                <span class="tabbar-label"><?=_lang("Reservations")?></span>
            </a>
    <?php endif; ?>
    </div>
</div>