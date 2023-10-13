<div id="view-offers" class="view tab <?=$active_tab=="offer"?"tab-active":""?>">
    <div data-name="search" class="page no-navbar page-current">
        <div class="page-content page-search">
            <div class="searchbar-backdrop"></div>
            <div class="block searchbar-container">
                <form class="searchbar searchbar-enabled">
                    <div class="searchbar-inner">
                        <div class="searchbar-input-wrap">
                            <input type="search" placeholder="<?=_lang("Search")?>" id="search">
                            <i class="searchbar-icon"></i>
                            <div class="preloader hidden"></div>
                            <span class="input-clear-button"></span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="block search-container">
                <div class="search-results" style="display: block;">
                    <ul class="list media-list post-list">
                    </ul>
                    <br>
                    <div class="loading hidden">
                        <?=_lang("Loading...")?>
                    </div>
                    <div class="no-result hidden">
                        <?=_lang("No Result Found")?>
                    </div>
                    <a class="big-button button button-fill link hidden load-more" data-page="1"><?=_lang("See more")?></a>

                </div>
            </div>
        </div>
    </div>
</div>


