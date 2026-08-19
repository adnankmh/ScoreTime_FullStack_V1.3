<?php
return [
 'release' => '1.2.0',
 'default_locale' => 'en',
 'supported_locales' => ['en','ar','fr','es','de','tr'],
 'editorial' => [
   'require_attribution' => true,
   'require_human_review' => env('EDITORIAL_HUMAN_REVIEW', true),
   'allow_auto_publish' => false,
   'max_summary_words' => 180,
 ],
 'smart_alerts' => [
   'goal','red_card','kickoff','halftime','fulltime','lineup','var','transfer','breaking_news'
 ],
 'experience_presets' => ['onboarding','login','home','match','team','player','competition'],
];
