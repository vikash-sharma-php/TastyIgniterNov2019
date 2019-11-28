<!--start-home-->
<div class="banner inner_pages" id="home">
    <div class="header-bottom wow slideInDown" data-wow-duration="1s" data-wow-delay=".3s">
        <div class="container">
            <div class="fixed-header">
                <?= partial('nav/logo'); ?>
                <div class="top-menu">
                    <span class="menu"> </span>
                    <nav class="link-effect-41" id="link-effect-41">
                        <ul class="nav navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="<?= restaurant_url('local/menus'); ?>" class="<?= ($this->page->getId() == 'local-menus') ? 'active' : ''; ?>"><?= lang('main::lang.menu_about'); ?></a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="<?= assets_url('/media/menu/menu.pdf'); ?>" target="_blank"><?= lang('main::lang.menu'); ?></a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="<?= page_url('reservation/reservation'); ?>" class="<?= ($this->page->getId() == 'reservation-reservation') ? 'active' : ''; ?>"><?= lang('main::lang.menu_reservation'); ?></a>
                            </li>

                            <?php if (Auth::isLogged()) { ?>
                                <li class="nav-item dropdown">
                                    <a id="dropdownAccount" class="nav-link dropdown-toggle clickable" role="button" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= lang('main::lang.menu_my_account'); ?> </a>

                                    <div class="dropdown-menu" role="menu" aria-labelledby="dropdownAccount">
                                        <a href="<?= page_url('account/orders'); ?>" class="dropdown-item <?= ($this->page->getId() == 'account-orders') ? 'active' : ''; ?>"><?= lang('main::lang.menu_recent_order'); ?></a>
                                        <a href="<?= page_url('account/account'); ?>" class="dropdown-item <?= ($this->page->getId() == 'account-account') ? 'active' : ''; ?>"><?= lang('main::lang.menu_my_account'); ?></a>
                                        <a href="<?= page_url('account/address'); ?>" class="dropdown-item <?= ($this->page->getId() == 'account-address') ? 'active' : ''; ?>"><?= lang('main::lang.menu_address'); ?></a>

                                        <a href="<?= page_url('account/reservations'); ?>" class="dropdown-item <?= ($this->page->getId() == 'account-reservations') ? 'active' : ''; ?>"><?= lang('main::lang.menu_recent_reservation'); ?></a>

                                        <a class="dropdown-item" href="javascript:void(0)" data-request="session::onLogout"><?= lang('main::lang.menu_logout'); ?></a>
                                    </div>
                                </li>
                            <?php } else { ?>
                                <li class="nav-item">
                                    <a href="<?= page_url('account/login'); ?>" class="nav-link <?= ($this->page->getId() == 'account-login') ? 'active' : ''; ?>"><?= lang('main::lang.menu_login'); ?></a>
                                </li>
                                <?php if ((bool) setting('allow_registration', true)) { ?>
                                    <li class="nav-item">
                                        <a href="<?= page_url('account/register'); ?>" class="nav-link <?= ($this->page->getId() == 'account-register') ? 'active' : ''; ?>"><?= lang('main::lang.menu_register'); ?></a>
                                    </li>
                                <?php } ?>
                            <?php } ?>

                            <?php if (!empty($headerPageList)) foreach ($headerPageList as $page) { ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= page_url('pages', ['slug' => $page->permalink_slug]); ?>"><?= $page->name; ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </nav>
                </div>

                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <?php if(Request::path() == 'default/menus'){ ?>
    <div loder__right><img src="<?= assets_url('/vd-theme/images/halal.png') ?>" /> </div>
    <?php } ?>
    <!-- <div class="down"><a class="scroll" href="#services"><img src=<?= '"' . assets_url(); ?>/vd-theme/images/down.png" alt=""></a>
</div> -->
</div>
<style>
    [loder__right] {
        width: 100px;
        overflow: hidden;
        position: absolute;
        bottom: 20px;
        right: 10px;
    }

    [loder__right] img{max-width:100%}
</style>