<?php

namespace Drupal\rusa_book\Plugin\Block;

use Drupal\book\Plugin\Block\BookNavigationBlock;

/**
 * Provides a 'Book navigation' block.
 *
 * @Block(
 *   id = "rusa_book_navigation",
 *   admin_label = @Translation("RUSA Book navigation"),
 *   category = @Translation("Menus")
 * )
 */
class RusaBookNavigationBlock extends BookNavigationBlock {

      /**
       * {@inheritdoc}
       */
      public function build() {
        $current_bid = 0;

        if ($node = $this->requestStack->getCurrentRequest()->get('node')) {
          $current_bid = empty($node->book['bid']) ? 0 : $node->book['bid'];
        }
        if ($this->configuration['block_mode'] == 'all pages') {
          return parent::build();
        }
        elseif ($current_bid) {
          // Only display this block when the user is browsing a book and do
          // not show unpublished books.
          $nid = \Drupal::entityQuery('node')
            ->condition('nid', $node->book['bid'], '=')
            ->condition('status', NODE_PUBLISHED)
            ->execute();

          // Only show the block if the user has view access for the top-level node.
          if ($nid) {
            $toc = $this->bookManager->getTableOfContents($node->book['bid'], $node->book);
            $tocvals = array_values($toc);
            $title = array_shift($tocvals);
            $tree = $this->bookManager->bookTreeAllData($node->book['bid'], $node->book);
            $content = $this->bookManager->bookTreeOutput($tree);
            return [
                '#theme' => 'rusa_book_template',
                '#title' => $this->t($title),
                '#content' => $content,
            ];
          }
        }
        return array();
      }

}
