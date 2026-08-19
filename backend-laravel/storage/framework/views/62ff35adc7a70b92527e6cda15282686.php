<?php $__env->startSection('content'); ?>
<?php ($blocks=count($homeBlocks??[]) ? $homeBlocks : [['type'=>'hero','enabled'=>true],['type'=>'live_matches','enabled'=>true,'limit'=>6],['type'=>'breaking_news','enabled'=>true,'limit'=>5],['type'=>'competitions','enabled'=>true,'limit'=>10],['type'=>'latest_news','enabled'=>true,'limit'=>9]]); ?>
<?php ($branding=$remoteDesign['branding']??[]); ?>
<?php $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 <?php if(($block['enabled']??true)!==true) continue; ?>
 <?php ($limit=(int)($block['limit']??6)); ?>
 <?php switch($block['type']??''):
  case ('hero'): ?>
   <section class="hero"><div class="panel hero-main"><span class="eyebrow">LIVE FOOTBALL • NEWS • DATA</span><h1><?php echo e($block['title']??(app()->getLocale()==='ar'?'كل كرة القدم في مكان واحد':'Football, unified.')); ?></h1><p class="muted"><?php echo e($branding['tagline']??(app()->getLocale()==='ar'?'نتائج مباشرة، أخبار، بطولات، إحصائيات وتنبيهات مخصصة.':'Live scores, editorial coverage, competitions and personalized alerts.')); ?></p><div><a class="btn primary" href="/matches"><?php echo e(__('ui.matches')); ?></a> <a class="btn ghost" href="/news"><?php echo e(__('ui.news')); ?></a></div></div><div class="metric-grid"><div class="metric"><span class="eyebrow">LIVE</span><strong><?php echo e($live->count()); ?></strong><span class="muted"><?php echo e(__('ui.matches')); ?></span></div><div class="metric"><span class="eyebrow">TODAY</span><strong><?php echo e($matches->count()); ?></strong><span class="muted"><?php echo e(__('ui.matches')); ?></span></div><div class="metric"><span class="eyebrow">NEWS</span><strong><?php echo e($news->count()); ?></strong><span class="muted"><?php echo e(__('ui.news')); ?></span></div><div class="metric"><span class="eyebrow">LEAGUES</span><strong><?php echo e($competitions->count()); ?></strong><span class="muted"><?php echo e(__('ui.competitions')); ?></span></div></div></section>
  <?php break; ?>
  <?php case ('live_matches'): ?> <?php case ('favorite_matches'): ?>
   <?php ($list=($block['type']==='live_matches'?$live:$matches)->take($limit)); ?>
   <div class="section-head"><div><span class="eyebrow"><?php echo e($block['type']==='live_matches'?'LIVE NOW':'MATCH CENTER'); ?></span><h2><?php echo e(__('ui.matches')); ?></h2></div><a class="link" href="/matches"><?php echo e(__('ui.matches')); ?> →</a></div><div class="match-grid"><?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><a class="match-card" href="<?php echo e(route('match.show',$m)); ?>"><div class="pill"><?php echo e($m->competition?->name_ar ?? 'Football'); ?></div><p class="muted"><?php echo e($m->kickoff_at?->format('H:i')); ?> · <?php echo e($m->round); ?></p><h3><?php echo e($m->homeTeam?->name_ar); ?> <span class="score"><?php echo e($m->home_score); ?> - <?php echo e($m->away_score); ?></span> <?php echo e($m->awayTeam?->name_ar); ?></h3><?php if(in_array($m->status,['live','halftime'])): ?><span class="live">● <?php echo e($m->minute); ?>' <?php echo e(__('ui.live')); ?></span><?php endif; ?></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><div class="panel">No matches right now.</div><?php endif; ?></div>
  <?php break; ?>
  <?php case ('breaking_news'): ?>
   <?php ($items=$news->where('is_breaking',true)->take($limit)); ?>
   <div class="section-head"><div><span class="eyebrow">BREAKING</span><h2><?php echo e(__('ui.news')); ?></h2></div></div><div class="news-grid"><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a class="news-card" href="<?php echo e(route('news.show',$a->slug)); ?>"><span class="live">● BREAKING</span><h3><?php echo e($a->title); ?></h3><p class="muted"><?php echo e($a->excerpt); ?></p></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
  <?php break; ?>
  <?php case ('latest_news'): ?>
   <div class="section-head"><div><span class="eyebrow">EDITORIAL</span><h2><?php echo e(__('ui.news')); ?></h2></div><a class="link" href="/news"><?php echo e(__('ui.news')); ?> →</a></div><div class="news-grid"><?php $__currentLoopData = $news->take($limit); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a class="news-card" href="<?php echo e(route('news.show',$a->slug)); ?>"><span class="pill"><?php echo e($a->category); ?></span><h3><?php echo e($a->title); ?></h3><p class="muted"><?php echo e($a->excerpt); ?></p></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
  <?php break; ?>
  <?php case ('competitions'): ?>
   <div id="competitions" class="section-head"><div><span class="eyebrow">COMPETITIONS</span><h2><?php echo e(__('ui.competitions')); ?></h2></div></div><div class="news-grid"><?php $__currentLoopData = $competitions->take($limit); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a class="news-card" href="<?php echo e(route('competition.show',$c)); ?>"><span class="pill"><?php echo e($c->country); ?></span><h3><?php echo e($c->name_ar); ?></h3><p class="muted"><?php echo e($c->season); ?></p></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
  <?php break; ?>
  <?php case ('transfers'): ?>
   <div class="section-head"><div><span class="eyebrow">MARKET</span><h2>Transfers</h2></div><a class="link" href="/transfers">Open →</a></div><div class="panel">Transfer Intelligence is enabled. Open the Transfer Center for the full feed.</div>
  <?php break; ?>
  <?php default: ?>
   <div class="panel"><span class="eyebrow"><?php echo e($block['type']??'BLOCK'); ?></span><p class="muted">This block is ready for its module renderer.</p></div>
 <?php endswitch; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ScoreTime_FullStack_V1.3\backend-laravel\resources\views/home/index.blade.php ENDPATH**/ ?>