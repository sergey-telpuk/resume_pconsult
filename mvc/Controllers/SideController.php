<?php
class SideController extends IController{
	private $_view;
	private $_dbuser;

	public function __construct ()
	{
		parent::__construct ();
		$this->_view   = new View();
		$this->_dbuser = new User();
	}

	public function positionAction(){
		if(isset($_POST['savePosition'])){
			$checkForm = $this->_checkFormPosition($_POST);
			foreach($checkForm as $input){
				if(isset($input['val']['message']) || $input['val']===false){
					return $this->_view->render(array(
						'view' => 'side/position',
						'data'=>array('inputs'=>$checkForm),
						'js'=>$this->_jsPosition(),
					));
				}
			}
			$this->_dbuser->updatePosition($checkForm, $this->getSessionUserID('user'));
			$this->headerLocation('index');
		}else{
			$checkForm = $this->_dbuser->selectPosition($this->getSessionUserID('user'));
			return $this->_view->render(array(
				'view' => 'side/position',
				'data'=>array('inputs'=>$checkForm),
				'js'=>$this->_jsPosition(),
			));
		}
	}

	public function educationAction(){
		if(isset($_POST['saveEducation'])){
			$checkForm = $this->_checkFormEducation($_POST);
			foreach($checkForm as $inputs){
				$vals = (array)$inputs['val'];
				foreach($vals as $key=>$val) {
					if (array_key_exists ('message', (array)$val) || $key==='message') {
						return $this->_view->render (array (
							'view' => 'side/education',
							'data' => array (
								'table_base_education_count' => count($checkForm['names_institutions']['value']),
								'table_training_courses_count' =>count($checkForm['courses_names']['value']),
								'inputs'      => $checkForm),
							'js'   => $this->_jsEducation()
						));
					}
				}
			}
			$this->_dbuser->updateEducation($checkForm, $this->getSessionUserID('user'));
			$this->headerLocation('index');
		}else{
			$checkForm = $this->_dbuser->selectEducation($this->getSessionUserID('user'));
			return $this->_view->render(array(
				'view' => 'side/education',
				'data'=>array(
					'table_base_education_count'=>count($checkForm['names_institutions']['value']),
					'table_training_courses_count' =>count($checkForm['courses_names']['value']),
					'inputs'=>$checkForm),
				'js'=>$this->_jsEducation()
			));
		}
	}

	public function experienceAction(){
		if(isset($_POST['saveExperience'])){
			$checkForm = $this->_checkFormExperience($_POST);
			foreach($checkForm as $inputs){
				$vals = (array)$inputs['val'];
				foreach($vals as $key=>$val) {
					if (array_key_exists ('message', (array)$val) || $key==='message') {
						return $this->_view->render (array (
							'view' => 'side/experience',
							'data' => array (
								'table_count_work' => count($checkForm['organizations']['value']),
								'table_count_recommendations' =>count($checkForm['recommend_names']['value']),
								'inputs'      => $checkForm),
							'js'   => $this->_jsExperience ()
						));
					}
				}
			}
			$this->_dbuser->updateExperience($checkForm, $this->getSessionUserID('user'));
			$this->headerLocation('index');
		}else{
			$checkForm = $this->_dbuser->selectExperience($this->getSessionUserID('user'));
			return $this->_view->render(array(
				'view' => 'side/experience',
				'data'=>array(
					'table_count_work'=>count($checkForm['organizations']['value']),
					'table_count_recommendations' =>count($checkForm['recommend_names']['value']),
					'inputs'=>$checkForm),
				'js'=>$this->_jsExperience()
			));
		}
	}

	private function _checkFormEducation($post){
		$education_base_key = 0;

		$level = trim(strip_tags($post['level']));

		$level_val = call_user_func(function ($level) {
			return !empty($level) ? true : array('message' => 'Необходимо заполнить');
		}, $level);

		foreach($post['names_institutions'] as $key=>$name_institution) {

			$names_institutions[$education_base_key] = trim(strip_tags($name_institution));
			$faculties[$education_base_key] = trim(strip_tags($post['faculties'][$key]));
			$specialties_specialties[$education_base_key] = trim(strip_tags($post['specialties_specialties'][$key]));
			$years_graduations[$education_base_key] = trim(strip_tags($post['years_graduations'][$key]));


			$faculties_val[$education_base_key] = call_user_func(function ($faculty) {
				return !empty($faculty) ? true : array('message' => 'Необходимо заполнить');
			}, $faculties[$education_base_key]);

			$names_institutions_val[$education_base_key] = call_user_func(function ($name_institution) {
				return !empty($name_institution) ? true : array('message' => 'Необходимо заполнить');
			}, $names_institutions[$education_base_key]);

			$specialties_specialties_val[$education_base_key] = call_user_func(function ($specialty_specialty) {
				return !empty($specialty_specialty) ? true : array('message' => 'Необходимо заполнить');
			}, $specialties_specialties[$education_base_key]);

			$years_graduations_val[$education_base_key] = call_user_func(function ($year_graduation) {
				return !empty($year_graduation) ? true : array('message' => 'Необходимо заполнить');
			}, $years_graduations[$education_base_key]);

			++$education_base_key;
		}

		$education_course_key = 0;

		foreach($post['courses_names'] as $key=>$course_name) {

			if((!empty($course_name)||!empty($post['follow_organizations'][$key])||
				!empty($post['courses_specialties'][$key])||
				!empty($post['course_years_graduations'][$key]))){

				$courses_names[$education_course_key] = trim(strip_tags($course_name));
				$follow_organizations[$education_course_key] = trim(strip_tags($post['follow_organizations'][$key]));
				$courses_specialties[$education_course_key] = trim(strip_tags($post['courses_specialties'][$key]));
				$course_years_graduations[$education_course_key] = trim(strip_tags($post['course_years_graduations'][$key]));

				$courses_names_val[$education_course_key] = call_user_func(function($var) {
					return !empty($var) ? true : array('message' => 'Необходимо заполнить');
				}, $courses_names[$education_course_key]);

				$follow_organizations_val[$education_course_key] = call_user_func(function ($var) {
					return !empty($var) ? true : array('message' => 'Необходимо заполнить');
				}, $follow_organizations[$education_course_key]);

				$courses_specialties_val[$education_course_key] = call_user_func(function ($var) {
					return !empty($var) ? true : array('message' => 'Необходимо заполнить');
				}, $courses_specialties[$education_course_key]);

				$course_years_graduations_val[$education_course_key] = call_user_func(function ($var) {
					return !empty($var) ? true : array('message' => 'Необходимо заполнить');
				}, $course_years_graduations[$education_course_key]);


				++$education_course_key;

			}
		}

		return array(
			'level'=>array(
				'val'=>$level_val,
				'value'=>$level
			),
			'names_institutions'=>array(
				'val'=>$names_institutions_val,
				'value'=>$names_institutions
			),
			'faculties'=>array(
				'val'=>$faculties_val,
				'value'=>$faculties
			),
			'specialties_specialties'=>array(
				'val'=>$specialties_specialties_val,
				'value'=>$specialties_specialties
			),
			'years_graduations'=>array(
				'val'=>$years_graduations_val,
				'value'=>$years_graduations
			),
			'courses_names'=>array(
				'val'=>(array)$courses_names_val,
				'value'=>(array)$courses_names
			),
			'follow_organizations'=>array(
				'val'=>(array)$follow_organizations_val,
				'value'=>(array)$follow_organizations
			),
			'courses_specialties'=>array(
				'val'=>(array)$courses_specialties_val,
				'value'=>(array)$courses_specialties
			),
			'course_years_graduations'=>array(
				'val'=>(array)$course_years_graduations_val,
				'value'=>(array)$course_years_graduations
			)
		);

	}

	private function _checkFormExperience($post){
		//			echo "<pre>";
		//			print_r($post);
		$organizations_key=0;

		foreach($post['organizations'] as $key=>$organization){

			$organizations[$organizations_key] = trim(strip_tags($organization));

			$organizations_val[$organizations_key] = call_user_func(function($organization){
				return !empty($organization)?true: array('message'=>'Необходимо заполнить');
			}, $organizations[$organizations_key]);


			$regions[$organizations_key] = trim(strip_tags(mb_eregi_replace('[^A-Za-zА-Яа-яёЁ]','', $post['regions'][$key])));

			$regions_val[$organizations_key]=call_user_func(function($regions){
				return !empty($regions)?true: array('message'=>'Необходимо заполнить');
			}, $regions[$organizations_key]);

			$sites[$organizations_key] = trim(strip_tags($post['sites'][$key]));

			$field_activities[$organizations_key] = trim(strip_tags($post['field_activities'][$key]));

			$positions[$organizations_key] = trim(strip_tags(mb_eregi_replace('[^A-Za-zА-Яа-яёЁ-]','', $post['positions'][$key])));

			$functions[$organizations_key] = trim(strip_tags($post['functions'][$key]));

			$functions_val[$organizations_key]=call_user_func(function($position){
				return !empty($position)?true: array('message'=>'Необходимо заполнить');
			}, $functions[$organizations_key]);

			$positions_val[$organizations_key] = call_user_func(function($position){
				return !empty($position)?true: array('message'=>'Необходимо заполнить');
			}, $positions);


			$getting_starteds[$organizations_key]=$post['getting_starteds'][$key];
			$getting_starteds_val[$organizations_key] = call_user_func(function($getting_started){
				if($getting_started['year'] == 0){
					return array('message'=>'Необходимо заполнить');
				}elseif($getting_started['year'] > date('Y')){
					return array('message'=>'Слишком поздно');
				}

			}, $getting_starteds[$organizations_key]);


			$at_the_moments[$organizations_key] = isset($post['at_the_moments'][$key])?$post['at_the_moments'][$key]:'false';

			$closing_works[$organizations_key] = $at_the_moments[$organizations_key]==='false'?$post['closing_works'][$key]:array('month' => 1, 'year' => 0);


			$closing_works_val[$organizations_key] = call_user_func(function($closing_work, $getting_started, $at_the_moments){
				if($at_the_moments === 'false' && $closing_work['year']==0){
					return array('message'=>'Необходимо заполнить');
				}elseif(($closing_work['year'] < $getting_started['year'] && $at_the_moments === 'false')||
					($closing_work['year'] === $getting_started['year'] && $closing_work['month']<$getting_started['month'])){
					return array('message'=>'Дата окончания ранее даты начала');
				}

			}, $closing_works[$organizations_key], $getting_starteds[$organizations_key], $at_the_moments[$organizations_key]);
			++$organizations_key;
		}


		$recommend_name_key = 0;

		foreach($post['recommend_names'] as $key=>$recommend_name){
			if(!empty($recommend_name)||!empty($post['recommend_position'][$key])||
				!empty($post['recommend_organization'][$key])||!empty($post['recommend_phone'][$key])){

				$recommend_names[$recommend_name_key] = trim(strip_tags($recommend_name));
				$recommend_position[$recommend_name_key] = trim(strip_tags($post['recommend_position'][$key]));
				$recommend_organization[$recommend_name_key] = trim(strip_tags($post['recommend_organization'][$key]));
				$recommend_phone[$recommend_name_key] = trim(strip_tags($post['recommend_phone'][$key]));

				$recommend_names_val[$recommend_name_key] = call_user_func(function($recommend_name){
					return !empty($recommend_name)?true: array('message'=>'Необходимо заполнить');
				},$recommend_names[$recommend_name_key]);

				$recommend_position_val[$recommend_name_key] = call_user_func(function($recommend_position){
					return !empty($recommend_position)?true: array('message'=>'Необходимо заполнить');
				},$recommend_position[$recommend_name_key]);

				$recommend_organization_val[$recommend_name_key] = call_user_func(function($recommend_organization){
					return !empty($recommend_organization)?true: array('message'=>'Необходимо заполнить');
				},$recommend_organization[$recommend_name_key]);

				$recommend_phone_val[$recommend_name_key] = call_user_func(function($recommend_phone){
					return !empty($recommend_phone)?true: array('message'=>'Необходимо заполнить');
				},$recommend_phone[$recommend_name_key]);

				++$recommend_name_key;
			}
		}



		$key_skills = $post['key_skills'];

		$key_skills_val = call_user_func(function($key_skills){
			return !empty($key_skills)?true: array('message'=>'Необходимо заполнить');
		}, $key_skills);

		$about_self = $post['about_self'];

		return array(
			'organizations'=>array(
				'val'=>$organizations_val,
				'value'=>$organizations
			),
			'positions'=>array(
				'val'=>$positions_val,
				'value'=>$positions
			),
			'getting_starteds'=>array(
				'val'=>$getting_starteds_val,
				'value'=>$getting_starteds
			),

			'closing_works'=>array(
				'val'=>$closing_works_val,
				'value'=>$closing_works
			),
			'at_the_moments'=>array('value'=>$at_the_moments),
			'regions'=>array(
				'val'=>$regions_val,
				'value'=>$regions
			),
			'sites'=>array('value'=>$sites),
			'field_activities'=>array('value'=>$field_activities),
			'functions'=>array(
				'val'=>$functions_val,
				'value'=>$functions
			),
			'key_skills'=>array(
				'val'=>$key_skills_val,
				'value'=>$key_skills
			),
			'about_self'=>array('value'=>$about_self),

			'recommend_names'=>array(
				'val'=>(array)$recommend_names_val,
				'value'=>(array)$recommend_names,
			),
			'recommend_position'=>array(
				'val'=>(array)$recommend_position_val,
				'value'=>(array)$recommend_position,
			),
			'recommend_organization'=>array(
				'val'=>(array)$recommend_organization_val,
				'value'=>(array)$recommend_organization,
			),
			'recommend_phone'=>array(
				'val'=>(array)$recommend_phone_val,
				'value'=>(array)$recommend_phone,
			)

		);

	}

	private function _checkFormPosition($post){

		$desired_position = isset($post['desired_position'])?trim(strip_tags($post['desired_position'])):'';
		$professional_area = isset($post['professional_area'])?trim(strip_tags($post['professional_area'])):'';
		$salary = isset($post['salary'])?trim(strip_tags(mb_eregi_replace('[^0-9]','',$post['salary']))):'';
		$currency = isset($post['currency'])?trim(strip_tags($post['currency'])):'';


		$employment = isset($post['employment'])?$post['employment']:array();
		$schedule = isset($post['schedule'])?$post['schedule']:array();


		$desired_position_val = call_user_func(function($desired_position){
			return !empty($desired_position)?true: array('message'=>'Необходимо заполнить');
		}, $desired_position);

		$professional_area_val = call_user_func(function($professional_area){
			return !empty($professional_area)?true: array('message'=>'Необходимо заполнить');
		}, $professional_area);

		$employment_val = call_user_func(function($employment){
			return count($employment)!==0?true: array('message'=>'Необходимо заполнить');
		}, $employment);

		$schedule_val = call_user_func(function($schedule){
			return count($schedule)!==0?true: array('message'=>'Необходимо заполнить');
		}, $schedule);

		$salary_val = call_user_func(function($salary){
			return (preg_match('/\d/',$salary) || empty($salary))?true: array('message'=>'Некорректные
				данные');
		}, $salary);

		return array(
			'desired_position'=>array(
				'val'=>$desired_position_val,
				'value'=>$desired_position ),
			'professional_area'=>array(
				'val'=>$professional_area_val,
				'value'=>$professional_area
			),
			'employment'=>array(
				'val'=>$employment_val,
				'value'=>$employment),
			'schedule'=>array(
				'val'=>$schedule_val,
				'value'=>$schedule
			),
			'salary'=>array(
				'val'=>$salary_val,
				'value'=>$salary
			),
			'currency'=>array('value'=>$currency),
		);
	}

	private function _jsPosition(){
		return array(
			'src'=>array(
				BASE_URL."/public/js/jquery-2.1.1.min.js",
				BASE_URL."/public/js/jquery.validate.min.js",
				BASE_URL."/public/js/position.js"
			),
		);
	}

	private function _jsEducation(){
		return array(
			'src'=>array(
				BASE_URL."/public/js/jquery-2.1.1.min.js",
				BASE_URL."/public/js/jquery.validate.min.js",
				BASE_URL."/public/js/handlebars-v2.0.0.js",
				BASE_URL."/public/js/education.js"
			),
		);
	}

	private function _jsExperience(){
		return array(
			'src'=>array(
				BASE_URL."/public/js/jquery-2.1.1.min.js",
				BASE_URL."/public/js/jquery.validate.min.js",
				BASE_URL."/public/js/handlebars-v2.0.0.js",
				BASE_URL."/public/js/experience.js"
			),
		);
	}
}