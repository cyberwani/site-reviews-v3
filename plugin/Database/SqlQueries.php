<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\QueryBuilder;

class SqlQueries
{
	protected $db;
	protected $postType;

	public function __construct()
	{
		global $wpdb;
		$this->db = $wpdb;
		$this->postType = Application::POST_TYPE;
	}

	/**
	 * @param string $sessionCookiePrefix
	 * @return int|false
	 */
	public function deleteAllSessions( $sessionCookiePrefix )
	{
		return $this->db->query("
			DELETE
			FROM {$this->db->options}
			WHERE option_name LIKE '{$sessionCookiePrefix}_%'
		");
	}

	/**
	 * @param string $expiredSessions
	 * @return int|false
	 */
	public function deleteExpiredSessions( $expiredSessions )
	{
		return $this->db->query("
			DELETE
			FROM {$this->db->options}
			WHERE option_name IN ('{$expiredSessions}')
		");
	}

	/**
	 * @param string $metaKey
	 * @return array
	 */
	public function getReviewCounts( $metaKey )
	{
		return (array) $this->db->get_results("
			SELECT m.meta_value AS name, COUNT(*) num_posts
			FROM {$this->db->posts} AS p
			INNER JOIN {$this->db->postmeta} AS m ON p.ID = m.post_id
			WHERE p.post_type = '{$this->postType}' AND m.meta_key = '{$metaKey}'
			GROUP BY name
		");
	}

	/**
	 * @param string $sessionCookiePrefix
	 * @param int $limit
	 * @return array
	 */
	public function getExpiredSessions( $sessionCookiePrefix, $limit )
	{
		return $this->db->get_results("
			SELECT option_name AS name, option_value AS expiration
			FROM {$this->db->options}
			WHERE option_name LIKE '{$sessionCookiePrefix}_expires_%'
			ORDER BY option_value ASC
			LIMIT 0, {$limit}
		");
	}

	/**
	 * @param string $metaReviewId
	 * @return int
	 */
	public function getReviewPostId( $metaReviewId )
	{
		$postId = $this->db->get_var("
			SELECT p.ID
			FROM {$this->db->posts} AS p
			INNER JOIN {$this->db->postmeta} AS pm ON p.ID = pm.post_id
			WHERE p.post_type = '{$this->postType}'
			AND pm.meta_key = 'review_id'
			AND pm.meta_value = '{$metaReviewId}'
		");
		return intval( $postId );
	}

	/**
	 * @param string $metaReviewType
	 * @return array
	 */
	public function getReviewIdsByType( $metaReviewType )
	{
		$query = $this->db->get_col("
			SELECT m1.meta_value AS review_id
			FROM {$this->db->posts} AS p
			INNER JOIN {$this->db->postmeta} AS m1 ON p.ID = m1.post_id
			INNER JOIN {$this->db->postmeta} AS m2 ON p.ID = m2.post_id
			WHERE p.post_type = '{$this->db->postType}'
			AND m1.meta_key = 'review_id'
			AND m2.meta_key = 'review_type'
			AND m2.meta_value = '{$metaReviewType}'
		");
		return array_keys( array_flip( $query ));
	}

	/**
	 * @param string|array $keys
	 * @param string $status
	 * @return array
	 */
	public function getReviewsMeta( $keys, $status )
	{
		$queryBuilder = glsr( QueryBuilder::class );
		$keys = $queryBuilder->buildSqlOr( $keys, "pm.meta_key = '%s'" );
		$status = $queryBuilder->buildSqlOr( $status, "p.post_status = '%s'" );
		return $this->db->get_col("
			SELECT DISTINCT pm.meta_value FROM {$this->db->postmeta} pm
			LEFT JOIN {$this->db->posts} p ON p.ID = pm.post_id
			WHERE p.post_type = '{$this->postType}'
			AND ({$keys})
			AND ({$status})
			ORDER BY pm.meta_value
		");
	}
}
