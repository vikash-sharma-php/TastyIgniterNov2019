<!--start-home-->
<div class="banner" id="home">
   <div class="header-bottom wow slideInDown" data-wow-duration="1s" data-wow-delay=".3s">
      <div class="container">
         <div class="fixed-header">
            <?= partial('nav/logo'); ?>
            <div class="top-menu">
               <span class="menu"> </span>
               <nav class="link-effect-4" id="link-effect-4">
                  <ul>
                     <li class="active"><a data-hover="Home" href="<?= page_url('home'); ?>"><?= sprintf(lang('main::lang.menu_home')) ?></a></li>
                     <li><a href="<?= restaurant_url('local/menus'); ?>"><?= sprintf(lang('main::lang.menu_about')) ?></a></li>
                     <!--li><a href="#about" class="scroll"><?= sprintf(lang('main::lang.menu_about')) ?></a></li-->      
                     <li><a data-hover="Menu" href="<?= assets_url('/media/menu/menu.pdf') ?>" target="_blank"><?= sprintf(lang('main::lang.menu')) ?></a></li>
                     <li><a data-hover="Services" href="#services" class="scroll"><?= sprintf(lang('main::lang.local.text_tab_services')) ?></a></li>
                     <!-- <li><a data-hover="Chefs" href="#team" class="scroll"><?= sprintf(lang('main::lang.local.text_tab_chefs')) ?></a></li> -->
                     <!-- <li><a data-hover="Reservation" href="#reservation" class="scroll">REservation</a></li> -->
                     <li><a data-hover="Gallery" href="#gallery" class="scroll"><?= sprintf(lang('main::lang.local.text_tab_gallery')) ?></a></li>
                     <li><a data-hover="Contact" href="#contact" class="scroll"><?= sprintf(lang('main::lang.contact.title')) ?></a></li>
                  </ul>
               </nav>
            </div>

            <div class="clearfix"></div>
         </div>
      </div>
   </div>
   <div class="banner-slider">
      <div class="callbacks_container">
         <ul class="rslides" id="slider4">
            <li>
               <div class="banner-info">
                  <h3 class="wow slideInUp" data-wow-duration="1s" data-wow-delay=".3s"><?= sprintf(lang('main::lang.home.text_step_banner_headline_one')) ?></h3>
                  <p class="wow slideInDown" data-wow-duration="1s" data-wow-delay=".3s"><?= sprintf(lang('main::lang.home.text_step_banner_text_one')) ?></p>
                  <div class="arrows wow slideInDown" data-wow-duration="1s" data-wow-delay=".2s"><img src=<?= '"' . assets_url(); ?>/vd-theme/images/border.png" alt="border" /></div>
                  <span class="wow slideInUp" data-wow-duration="1s" data-wow-delay=".3s"><?= sprintf(lang('main::lang.home.text_ready_to_be_opened')) ?></span>
               </div>
            </li>
            <li>
               <div class="banner-info">
                  <h3 class="wow slideInUp" data-wow-duration="1s" data-wow-delay=".3s"><?= sprintf(lang('main::lang.home.text_step_banner_headline_two')) ?></h3>
                  <p class="wow slideInDown" data-wow-duration="1s" data-wow-delay=".3s"><?= sprintf(lang('main::lang.home.text_step_banner_text_two')) ?></p>
                  <div class="arrows wow slideInDown" data-wow-duration="1s" data-wow-delay=".2s"><img src=<?= '"' . assets_url(); ?>/vd-theme/images/border.png" alt="border" /></div>
                  <span class="wow slideInUp" data-wow-duration="1s" data-wow-delay=".3s"><?= sprintf(lang('main::lang.home.text_ready_to_be_opened')) ?></span>
               </div>
            </li>
            <li>
               <div class="banner-info">
                  <h3 class="wow slideInUp" data-wow-duration="1s" data-wow-delay=".3s"><?= sprintf(lang('main::lang.home.text_step_banner_headline_three')) ?></h3>
                  <p class="wow slideInDown" data-wow-duration="1s" data-wow-delay=".3s"><?= sprintf(lang('main::lang.home.text_step_banner_text_three')) ?></p>
                  <div class="arrows wow slideInDown" data-wow-duration="1s" data-wow-delay=".2s"><img src=<?= '"' . assets_url(); ?>/vd-theme/images/border.png" alt="border" /></div>
                  <span class="wow slideInUpslideInLeft" data-wow-duration="1s" data-wow-delay=".3s"><?= sprintf(lang('main::lang.home.text_ready_to_be_opened')) ?></span>
               </div>
            </li>
         </ul>
      </div>

   </div>

   <div class="down"><a class="scroll" href="#services"><img src=<?= '"' . assets_url(); ?>/vd-theme/images/down.png" alt=""></a>
   </div>
</div>