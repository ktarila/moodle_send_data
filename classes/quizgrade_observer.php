<?php

namespace local_send_grade;

defined('MOODLE_INTERNAL') || die();

class quizgrade_observer
{
    /**
     * Handle the mod_quiz\event\attempt_submitted event.
     *
     * @param object $event The event object.
     */
    public static function send_grade($event)
    {
        global $CFG;
        global $DB;

        require_once($CFG->libdir . '/completionlib.php');
        // require_once("{$CFG->libdir}/accesslib.php");
        // require_once($CFG->libdir . '/datalib.php');

        $event_data = $event->get_data();
        // $course = new \stdClass();
        // $course->id = $event_data['courseid'];
        // $cinfo = new \completion_info($course);
        // $isComplete = $cinfo->is_course_complete($event_data['userid']);
        // var_dump($isComplete);

        $courseGrade = self::getGrade($event_data['userid'], $event_data['courseid']);

        // var_dump($courseGrade);

        // get user
        $user = $DB->get_record("user", ['id' => $event_data['userid']]);
        $courseGrade['user_email'] = $user->email;


        // send courseGrade to external api
        self::postGrade($courseGrade);
    }

    private static function getGrade($userid, $courseid)
    {
        global $CFG;
        require_once($CFG->libdir . '/gradelib.php');
        require_once($CFG->libdir . '/datalib.php');

        $mods = get_course_mods($courseid);
        $max = 0;
        $sum = 0;
        foreach ($mods as $cm) {
            $grades = grade_get_grades($courseid, 'mod', $cm->modname, $cm->instance, $userid);
            foreach ($grades->items as $grade) {
                $max += $grade->grademax;
                foreach ($grade->grades as $userGrade) {
                    $sum += $userGrade->grade;
                }
            }
        }
        return ['max_grade' => $max, 'grade' => $sum, 'course_id' => $courseid];
    }

    public static function getToken()
    {
        $send_grade_settings = get_config('local_send_grade');

        $ch = curl_init();


        $post_fields = [
            'username' => $send_grade_settings->username,
            'password' => $send_grade_settings->password,
        ];
        $url = $send_grade_settings->login;

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($post_fields),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));


        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $token = null;
        if ($httpcode === 200) {
            $json_data = mb_substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            $data = json_decode($json_data, true);
            if (isset($data['token'])) {
                return $data['token'];
            }
        }

        return $token;
    }

    private static function postGrade(array $grade)
    {
        $send_grade_settings = get_config('local_send_grade');
        $token = self::getToken();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $send_grade_settings->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($grade),
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer {$token}",
                'Content-Type: application/json'
            ),
        ));

        curl_exec($curl);

        curl_close($curl);
        return;
    }
}
