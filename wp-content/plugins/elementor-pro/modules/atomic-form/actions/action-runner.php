<?php
namespace ElementorPro\Modules\AtomicForm\Actions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Action_Runner {

	/**
	 * Registered actions.
	 *
	 * @var Action_Base[]
	 */
	private static $actions = [];

	/**
	 * Register an action.
	 *
	 * @param Action_Base $action Action instance to register.
	 * @return void
	 */
	public static function register_action( Action_Base $action ): void {
		self::$actions[ $action->get_type() ] = $action;
	}

	/**
	 * Create an action instance by type.
	 *
	 * @param string $type Action type.
	 * @return Action_Base|null Action instance or null if not found.
	 */
	public static function create_action( string $type ): ?Action_Base {
		if ( ! isset( self::$actions[ $type ] ) ) {
			return null;
		}

		return self::$actions[ $type ];
	}

	/**
	 * Get all registered actions.
	 *
	 * @return Action_Base[] Array of registered actions.
	 */
	public static function get_registered_actions(): array {
		return self::$actions;
	}

	/**
	 * Check if an action type is registered.
	 *
	 * @param string $type Action type.
	 * @return bool
	 */
	public static function has_action( string $type ): bool {
		return isset( self::$actions[ $type ] );
	}

	/**
	 * Execute multiple actions and gather results.
	 *
	 * @param string[] $actions Array of action type strings.
	 * @param array $form_data Sanitized form data.
	 * @param array $widget_settings Full widget settings for actions to extract what they need.
	 * @param array $context Form context (post_id, form_id, form_name).
	 * @return array Results containing actionResults, allActionsSucceeded, failedActions, and optional submissionId.
	 */
	public static function execute_actions( array $actions, array $form_data, array $widget_settings, array $context ): array {
		$action_results = [];
		$failed_actions = [];

		foreach ( $actions as $action_type ) {

			if ( ! Action_Type::is_valid( $action_type ) ) {
				$action_results[] = [
					'type' => $action_type,
					'status' => 'failed',
					'error' => sprintf( __( 'Invalid action type: %s', 'elementor-pro' ), $action_type ),
				];
				$failed_actions[] = $action_type;
				continue;
			}

			try {
				$action = self::create_action( $action_type );

				if ( ! $action ) {
					throw new \Exception( sprintf( __( 'Could not create action: %s', 'elementor-pro' ), $action_type ) );
				}

				$result = $action->execute( $form_data, $widget_settings, $context );

				$action_results[] = array_merge(
					[ 'type' => $action_type ],
					$result
				);
			} catch ( \Exception $e ) {
				$action_results[] = [
					'type' => $action_type,
					'status' => 'failed',
					'error' => $e->getMessage(),
				];
				$failed_actions[] = $action_type;
			}
		}

		$all_actions_succeeded = empty( $failed_actions );

		$response = [
			'actionResults' => $action_results,
			'allActionsSucceeded' => $all_actions_succeeded,
			'failedActions' => $failed_actions,
		];

		return $response;
	}

	/**
	 * Initialize default actions.
	 *
	 * @return void
	 */
	public static function init(): void {
		self::register_action( new Email_Action() );
		self::register_action( new Collect_Submissions_Action() );
		self::register_action( new Webhook_Action() );

		/**
		 * Allow registering custom actions.
		 *
		 * @param Action_Factory $factory The action factory instance.
		 */
		do_action( 'elementor_pro/atomic_forms/actions/register', __CLASS__ );
	}
}
