<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * NCTB (Bangladesh National Curriculum and Textbook Board) subject
 * catalogue used to auto-seed a new tenant's `subject` table.
 *
 * Structure
 * ---------
 *   $config['nctb_subjects']['levels'] = [
 *       <level_key> => [
 *           'name'     => human-readable level label,
 *           'classes'  => list of class_numeric values this level covers,
 *           'subjects' => list of subject rows for this level,
 *       ],
 *       ...
 *   ];
 *
 * Each subject row supplies:
 *   name  — English NCTB nomenclature (suffixed with the level to avoid
 *           collisions across SSC/HSC streams, e.g. "Bangla 1st Paper (SSC)")
 *   code  — NCTB textbook code where one exists, otherwise an internal
 *           short code (kept stable for joins on subject_code)
 *   type  — 'Theory' | 'Practical' | 'Optional' | 'Mandatory'
 *           Matches the existing /subject form's subject_type dropdown.
 *
 * Codes follow the NCTB textbook codes printed on the back cover of each
 * book where official codes are available; for newer / less-standardised
 * subjects we use NCTB-style 3-digit codes.
 *
 * Seeder usage:
 *   $this->load->library('nctb_subject_seeder');
 *   $result = $this->nctb_subject_seeder->seedForBranch($branchId);
 *
 * @author SmartSchool.bd
 */
/**
 * Standard NCTB Bangladesh class roster — the seeder uses this to make
 * sure every tenant has the canonical class list (Play .. Twelve) so
 * subject_assign rows can be wired up cleanly. `name_numeric` matches
 * the column on the existing `class` table and is the join key the
 * subject catalogue uses to figure out which classes each subject
 * belongs to.
 */
$config['nctb_classes'] = [
    ['name' => 'Play',     'name_numeric' => '0'],
    ['name' => 'Nursery',  'name_numeric' => '01'],
    ['name' => 'Class 1',  'name_numeric' => '1'],
    ['name' => 'Class 2',  'name_numeric' => '2'],
    ['name' => 'Class 3',  'name_numeric' => '3'],
    ['name' => 'Class 4',  'name_numeric' => '4'],
    ['name' => 'Class 5',  'name_numeric' => '5'],
    ['name' => 'Class 6',  'name_numeric' => '6'],
    ['name' => 'Class 7',  'name_numeric' => '7'],
    ['name' => 'Class 8',  'name_numeric' => '8'],
    ['name' => 'Class 9',  'name_numeric' => '9'],
    ['name' => 'Class 10', 'name_numeric' => '10'],
    ['name' => 'Class 11', 'name_numeric' => '11'],
    ['name' => 'Class 12', 'name_numeric' => '12'],
];

/**
 * Legacy class names that the seeder is allowed to auto-rename to the
 * current canonical name above. Keyed by `name_numeric`. If a class on
 * a tenant branch has the matching numeric AND its current `name`
 * exactly matches one of the entries in the corresponding legacy list,
 * the seeder will rename it to the canonical name. Any other name
 * (e.g. a school's custom name like "SSC Section") is left alone, so
 * customised class names are never overwritten.
 */
$config['nctb_class_legacy_names'] = [
    '0'  => ['Play', 'KG', 'KG-1', 'Pre-Primary', 'Pre Primary'],
    '01' => ['Nursery', 'KG-2'],
    '1'  => ['One', 'Class One', 'Standard 1', '1', 'Class-1', 'Class I'],
    '2'  => ['Two', 'Class Two', 'Standard 2', '2', 'Class-2', 'Class II'],
    '3'  => ['Three', 'Class Three', 'Standard 3', '3', 'Class-3', 'Class III'],
    '4'  => ['Four', 'Class Four', 'Standard 4', '4', 'Class-4', 'Class IV'],
    '5'  => ['Five', 'Class Five', 'Standard 5', '5', 'Class-5', 'Class V'],
    '6'  => ['Six', 'Class Six', 'Standard 6', '6', 'Class-6', 'Class VI'],
    '7'  => ['Seven', 'Class Seven', 'Standard 7', '7', 'Class-7', 'Class VII'],
    '8'  => ['Eight', 'Class Eight', 'Standard 8', '8', 'Class-8', 'Class VIII'],
    '9'  => ['Nine', 'Class Nine', 'Standard 9', '9', 'Class-9', 'Class IX'],
    '10' => ['Ten', 'Class Ten', 'Standard 10', '10', 'Class-10', 'Class X'],
    '11' => ['Eleven', 'Class Eleven', 'Standard 11', '11', 'Class-11', 'Class XI'],
    '12' => ['Twelve', 'Class Twelve', 'Standard 12', '12', 'Class-12', 'Class XII'],
];

/**
 * Default Section seeded onto every freshly-provisioned tenant so the
 * subject_assign foreign keys have something to point at. Tenants can
 * add more sections (B, C, …) later; new sections automatically inherit
 * the same subject roster via the Sections::save hook + the seeder's
 * `seedSubjectAssignsForBranch()`.
 */
$config['nctb_default_section'] = ['name' => 'A', 'capacity' => ''];

/**
 * NCTB Bangladesh GPA-5 grade scale. Seeded onto every tenant on
 * approval; admin can edit/delete/add from /grades.
 *   A+ : 80-100  → 5.00 (Excellent)
 *   A  : 70-79   → 4.00 (Very Good)
 *   A- : 60-69   → 3.50 (Good)
 *   B  : 50-59   → 3.00 (Satisfactory)
 *   C  : 40-49   → 2.00 (Acceptable)
 *   D  : 33-39   → 1.00 (Pass)
 *   F  : 0-32    → 0.00 (Fail)
 */
$config['nctb_grades'] = [
    ['name' => 'A+', 'grade_point' => '5.00', 'lower_mark' => 80, 'upper_mark' => 100, 'remark' => 'Excellent'],
    ['name' => 'A',  'grade_point' => '4.00', 'lower_mark' => 70, 'upper_mark' => 79,  'remark' => 'Very Good'],
    ['name' => 'A-', 'grade_point' => '3.50', 'lower_mark' => 60, 'upper_mark' => 69,  'remark' => 'Good'],
    ['name' => 'B',  'grade_point' => '3.00', 'lower_mark' => 50, 'upper_mark' => 59,  'remark' => 'Satisfactory'],
    ['name' => 'C',  'grade_point' => '2.00', 'lower_mark' => 40, 'upper_mark' => 49,  'remark' => 'Acceptable'],
    ['name' => 'D',  'grade_point' => '1.00', 'lower_mark' => 33, 'upper_mark' => 39,  'remark' => 'Pass'],
    ['name' => 'F',  'grade_point' => '0.00', 'lower_mark' => 0,  'upper_mark' => 32,  'remark' => 'Fail'],
];

/**
 * Default exam term roster — three terms used by most Bangladesh
 * schools. Seeded onto every tenant for the active session; admin
 * can rename / add more (eg "Pre-Test", "Class Test 1") from
 * /exam_term.
 */
$config['nctb_exam_terms'] = [
    'First Term',
    'Half-Yearly',
    'Final',
];

/**
 * Default mark-distribution categories used by the exam module to
 * break a subject's total into pieces. Names follow NCTB convention.
 * Admin can edit/add from /mark_distribution.
 */
$config['nctb_mark_distributions'] = [
    'Theory',
    'Practical',
    'Subjective',
    'Objective',
    'CT',         // Class Test
    'MCQ',
];

/**
 * Default full / pass marks per distribution category. Keyed by the
 * canonical English name (case-insensitive lookup also matches the
 * Bangla aliases below). Used by the seeder when it builds the
 * `timetable_exam.mark_distribution` JSON so the mark-entry form has
 * a working `full_mark` / `pass_mark` per slice out of the box.
 *
 * Admin can rewrite per (class, section, subject, exam) from
 * /timetable → "Exam Schedule".
 */
$config['nctb_mark_distribution_defaults'] = [
    'theory'     => ['full_mark' => 100, 'pass_mark' => 33],
    'practical'  => ['full_mark' => 50,  'pass_mark' => 17],
    'subjective' => ['full_mark' => 70,  'pass_mark' => 23],
    'objective'  => ['full_mark' => 30,  'pass_mark' => 10],
    'ct'         => ['full_mark' => 20,  'pass_mark' => 7],
    'mcq'        => ['full_mark' => 30,  'pass_mark' => 10],
    // Bangla aliases used by _bnAlias() in Nctb_subject_seeder.
    'তত্ত্বীয়'   => ['full_mark' => 100, 'pass_mark' => 33],
    'ব্যবহারিক'  => ['full_mark' => 50,  'pass_mark' => 17],
    'রচনামূলক'   => ['full_mark' => 70,  'pass_mark' => 23],
    'বহুনির্বাচনি' => ['full_mark' => 30,  'pass_mark' => 10],
    'শ্রেণি অভীক্ষা (সিটি)' => ['full_mark' => 20, 'pass_mark' => 7],
    'এমসিকিউ'    => ['full_mark' => 30,  'pass_mark' => 10],
];

/**
 * Default starter exams seeded onto every tenant once the exam terms
 * + mark distributions are in place. Each entry creates one `exam`
 * row anchored to the matching term (resolved by name) with the
 * listed mark distribution slices (resolved by name → id and stored
 * as a JSON id list, matching the existing Exam_model::exam_save
 * format). Admin can rename / delete / add from /exam.
 *
 * type_id values match the column comment on `exam`:
 *   1 = mark only · 2 = GPA only · 3 = both (recommended default)
 *
 * publish_result = 1 makes the exam appear on the public
 * /<school>/exam_results lookup page so students/parents can
 * immediately see the demo report cards. Admin can unpublish from
 * /exam at any time.
 */
$config['nctb_starter_exams'] = [
    [
        'name'              => 'First Term Examination',
        'term_name'         => 'First Term',
        'type_id'           => 3,
        'mark_distribution' => ['Theory', 'Practical'],
        'remark'            => 'Auto-seeded NCTB starter exam — edit or replace as needed.',
        'publish'           => 1,
    ],
    [
        'name'              => 'Half-Yearly Examination',
        'term_name'         => 'Half-Yearly',
        'type_id'           => 3,
        'mark_distribution' => ['Theory', 'Practical'],
        'remark'            => 'Auto-seeded NCTB starter exam — edit or replace as needed.',
        'publish'           => 1,
    ],
    [
        'name'              => 'Final Examination',
        'term_name'         => 'Final',
        'type_id'           => 3,
        'mark_distribution' => ['Theory', 'Practical'],
        'remark'            => 'Auto-seeded NCTB starter exam — edit or replace as needed.',
        'publish'           => 1,
    ],
];

$config['nctb_subjects'] = [
    'levels' => [

        // ---- Primary (Class 1-5) ----
        'primary' => [
            'name'    => 'Primary (Class 1-5)',
            'classes' => ['1', '2', '3', '4', '5'],
            'subjects' => [
                ['name' => 'Bangla (Primary)',                         'code' => '101', 'type' => 'Theory'],
                ['name' => 'English (Primary)',                        'code' => '102', 'type' => 'Theory'],
                ['name' => 'Mathematics (Primary)',                    'code' => '103', 'type' => 'Theory'],
                ['name' => 'Bangladesh and Global Studies (Primary)',  'code' => '150', 'type' => 'Theory'],
                ['name' => 'Primary Science',                          'code' => '154', 'type' => 'Theory'],
                ['name' => 'Islam and Moral Education (Primary)',      'code' => '111', 'type' => 'Optional'],
                ['name' => 'Hindu Religion and Moral Education (Primary)',     'code' => '112', 'type' => 'Optional'],
                ['name' => 'Buddhist Religion and Moral Education (Primary)',  'code' => '113', 'type' => 'Optional'],
                ['name' => 'Christian Religion and Moral Education (Primary)', 'code' => '114', 'type' => 'Optional'],
                ['name' => 'Arts and Crafts (Primary)',                'code' => '160', 'type' => 'Theory'],
                ['name' => 'Physical Education and Health (Primary)',  'code' => '147', 'type' => 'Practical'],
            ],
        ],

        // ---- Junior Secondary (Class 6-8, JSC) ----
        'jsc' => [
            'name'    => 'Junior Secondary (Class 6-8)',
            'classes' => ['6', '7', '8'],
            'subjects' => [
                ['name' => 'Bangla (JSC)',                              'code' => '101', 'type' => 'Theory'],
                ['name' => 'English (JSC)',                             'code' => '107', 'type' => 'Theory'],
                ['name' => 'Mathematics (JSC)',                         'code' => '109', 'type' => 'Theory'],
                ['name' => 'Science (JSC)',                             'code' => '127', 'type' => 'Theory'],
                ['name' => 'Bangladesh and Global Studies (JSC)',       'code' => '150', 'type' => 'Theory'],
                ['name' => 'Information and Communication Technology (JSC)', 'code' => '154', 'type' => 'Theory'],
                ['name' => 'Agriculture Studies (JSC)',                 'code' => '134', 'type' => 'Optional'],
                ['name' => 'Home Science (JSC)',                        'code' => '151', 'type' => 'Optional'],
                ['name' => 'Arabic (JSC)',                              'code' => '121', 'type' => 'Optional'],
                ['name' => 'Sanskrit (JSC)',                            'code' => '123', 'type' => 'Optional'],
                ['name' => 'Pali (JSC)',                                'code' => '124', 'type' => 'Optional'],
                ['name' => 'Islam and Moral Education (JSC)',           'code' => '111', 'type' => 'Optional'],
                ['name' => 'Hindu Religion and Moral Education (JSC)',  'code' => '112', 'type' => 'Optional'],
                ['name' => 'Buddhist Religion and Moral Education (JSC)',  'code' => '113', 'type' => 'Optional'],
                ['name' => 'Christian Religion and Moral Education (JSC)', 'code' => '114', 'type' => 'Optional'],
                ['name' => 'Career Education (JSC)',                    'code' => '156', 'type' => 'Theory'],
                ['name' => 'Physical Education and Health (JSC)',       'code' => '147', 'type' => 'Practical'],
                ['name' => 'Arts and Crafts (JSC)',                     'code' => '160', 'type' => 'Theory'],
            ],
        ],

        // ---- SSC Core (Class 9-10) ----
        'ssc_core' => [
            'name'    => 'SSC Core (Class 9-10)',
            'classes' => ['9', '10'],
            'subjects' => [
                ['name' => 'Bangla 1st Paper (SSC)',                    'code' => '101', 'type' => 'Theory'],
                ['name' => 'Bangla 2nd Paper (SSC)',                    'code' => '102', 'type' => 'Theory'],
                ['name' => 'English 1st Paper (SSC)',                   'code' => '107', 'type' => 'Theory'],
                ['name' => 'English 2nd Paper (SSC)',                   'code' => '108', 'type' => 'Theory'],
                ['name' => 'General Mathematics (SSC)',                 'code' => '109', 'type' => 'Theory'],
                ['name' => 'Information and Communication Technology (SSC)', 'code' => '154', 'type' => 'Theory'],
                ['name' => 'Career Education (SSC)',                    'code' => '156', 'type' => 'Theory'],
                ['name' => 'Physical Education and Health (SSC)',       'code' => '147', 'type' => 'Practical'],
                ['name' => 'Islam and Moral Education (SSC)',           'code' => '111', 'type' => 'Optional'],
                ['name' => 'Hindu Religion and Moral Education (SSC)',  'code' => '112', 'type' => 'Optional'],
                ['name' => 'Buddhist Religion and Moral Education (SSC)',  'code' => '113', 'type' => 'Optional'],
                ['name' => 'Christian Religion and Moral Education (SSC)', 'code' => '114', 'type' => 'Optional'],
            ],
        ],

        // ---- SSC Science stream (Class 9-10) ----
        'ssc_sci' => [
            'name'    => 'SSC Science (Class 9-10)',
            'classes' => ['9', '10'],
            'subjects' => [
                ['name' => 'Physics (SSC) — Theory',          'code' => '136', 'type' => 'Theory'],
                ['name' => 'Physics (SSC) — Practical',       'code' => '136', 'type' => 'Practical'],
                ['name' => 'Chemistry (SSC) — Theory',        'code' => '137', 'type' => 'Theory'],
                ['name' => 'Chemistry (SSC) — Practical',     'code' => '137', 'type' => 'Practical'],
                ['name' => 'Biology (SSC) — Theory',          'code' => '138', 'type' => 'Theory'],
                ['name' => 'Biology (SSC) — Practical',       'code' => '138', 'type' => 'Practical'],
                ['name' => 'Higher Mathematics (SSC) — Theory',    'code' => '126', 'type' => 'Theory'],
                ['name' => 'Higher Mathematics (SSC) — Practical', 'code' => '126', 'type' => 'Practical'],
                ['name' => 'Agriculture Studies (SSC)',       'code' => '134', 'type' => 'Optional'],
            ],
        ],

        // ---- SSC Business Studies stream (Class 9-10) ----
        'ssc_biz' => [
            'name'    => 'SSC Business Studies (Class 9-10)',
            'classes' => ['9', '10'],
            'subjects' => [
                ['name' => 'Accounting (SSC)',                'code' => '146', 'type' => 'Theory'],
                ['name' => 'Business Entrepreneurship (SSC)', 'code' => '143', 'type' => 'Theory'],
                ['name' => 'Finance and Banking (SSC)',       'code' => '152', 'type' => 'Theory'],
            ],
        ],

        // ---- SSC Humanities stream (Class 9-10) ----
        'ssc_hum' => [
            'name'    => 'SSC Humanities (Class 9-10)',
            'classes' => ['9', '10'],
            'subjects' => [
                ['name' => 'History of Bangladesh and World Civilization (SSC)', 'code' => '153', 'type' => 'Theory'],
                ['name' => 'Geography and Environment (SSC)', 'code' => '110', 'type' => 'Theory'],
                ['name' => 'Civics and Citizenship (SSC)',    'code' => '140', 'type' => 'Theory'],
                ['name' => 'Economics (SSC)',                 'code' => '141', 'type' => 'Theory'],
            ],
        ],

        // ---- HSC Core (Class 11-12) ----
        'hsc_core' => [
            'name'    => 'HSC Core (Class 11-12)',
            'classes' => ['11', '12'],
            'subjects' => [
                ['name' => 'Bangla 1st Paper (HSC)',                    'code' => '101', 'type' => 'Theory'],
                ['name' => 'Bangla 2nd Paper (HSC)',                    'code' => '102', 'type' => 'Theory'],
                ['name' => 'English 1st Paper (HSC)',                   'code' => '107', 'type' => 'Theory'],
                ['name' => 'English 2nd Paper (HSC)',                   'code' => '108', 'type' => 'Theory'],
                ['name' => 'Information and Communication Technology (HSC)', 'code' => '275', 'type' => 'Theory'],
                ['name' => 'Islam and Moral Education (HSC)',           'code' => '249', 'type' => 'Optional'],
                ['name' => 'Hindu Religion and Moral Education (HSC)',  'code' => '250', 'type' => 'Optional'],
                ['name' => 'Buddhist Religion and Moral Education (HSC)',  'code' => '251', 'type' => 'Optional'],
                ['name' => 'Christian Religion and Moral Education (HSC)', 'code' => '252', 'type' => 'Optional'],
            ],
        ],

        // ---- HSC Science stream (Class 11-12) ----
        'hsc_sci' => [
            'name'    => 'HSC Science (Class 11-12)',
            'classes' => ['11', '12'],
            'subjects' => [
                ['name' => 'Physics 1st Paper (HSC) — Theory',    'code' => '174', 'type' => 'Theory'],
                ['name' => 'Physics 1st Paper (HSC) — Practical', 'code' => '174', 'type' => 'Practical'],
                ['name' => 'Physics 2nd Paper (HSC) — Theory',    'code' => '175', 'type' => 'Theory'],
                ['name' => 'Physics 2nd Paper (HSC) — Practical', 'code' => '175', 'type' => 'Practical'],
                ['name' => 'Chemistry 1st Paper (HSC) — Theory',    'code' => '176', 'type' => 'Theory'],
                ['name' => 'Chemistry 1st Paper (HSC) — Practical', 'code' => '176', 'type' => 'Practical'],
                ['name' => 'Chemistry 2nd Paper (HSC) — Theory',    'code' => '177', 'type' => 'Theory'],
                ['name' => 'Chemistry 2nd Paper (HSC) — Practical', 'code' => '177', 'type' => 'Practical'],
                ['name' => 'Biology 1st Paper (HSC) — Theory',     'code' => '178', 'type' => 'Theory'],
                ['name' => 'Biology 1st Paper (HSC) — Practical',  'code' => '178', 'type' => 'Practical'],
                ['name' => 'Biology 2nd Paper (HSC) — Theory',     'code' => '179', 'type' => 'Theory'],
                ['name' => 'Biology 2nd Paper (HSC) — Practical',  'code' => '179', 'type' => 'Practical'],
                ['name' => 'Higher Mathematics 1st Paper (HSC) — Theory',    'code' => '265', 'type' => 'Theory'],
                ['name' => 'Higher Mathematics 1st Paper (HSC) — Practical', 'code' => '265', 'type' => 'Practical'],
                ['name' => 'Higher Mathematics 2nd Paper (HSC) — Theory',    'code' => '266', 'type' => 'Theory'],
                ['name' => 'Higher Mathematics 2nd Paper (HSC) — Practical', 'code' => '266', 'type' => 'Practical'],
                ['name' => 'Statistics 1st Paper (HSC)', 'code' => '129', 'type' => 'Theory'],
                ['name' => 'Statistics 2nd Paper (HSC)', 'code' => '130', 'type' => 'Theory'],
            ],
        ],

        // ---- HSC Business Studies stream (Class 11-12) ----
        'hsc_biz' => [
            'name'    => 'HSC Business Studies (Class 11-12)',
            'classes' => ['11', '12'],
            'subjects' => [
                ['name' => 'Accounting 1st Paper (HSC)',                       'code' => '253', 'type' => 'Theory'],
                ['name' => 'Accounting 2nd Paper (HSC)',                       'code' => '254', 'type' => 'Theory'],
                ['name' => 'Business Organization and Management 1st Paper (HSC)', 'code' => '277', 'type' => 'Theory'],
                ['name' => 'Business Organization and Management 2nd Paper (HSC)', 'code' => '278', 'type' => 'Theory'],
                ['name' => 'Finance, Banking and Insurance 1st Paper (HSC)',   'code' => '292', 'type' => 'Theory'],
                ['name' => 'Finance, Banking and Insurance 2nd Paper (HSC)',   'code' => '293', 'type' => 'Theory'],
                ['name' => 'Production Management and Marketing 1st Paper (HSC)', 'code' => '286', 'type' => 'Theory'],
                ['name' => 'Production Management and Marketing 2nd Paper (HSC)', 'code' => '287', 'type' => 'Theory'],
                ['name' => 'Economics 1st Paper (HSC)',                        'code' => '109', 'type' => 'Theory'],
                ['name' => 'Economics 2nd Paper (HSC)',                        'code' => '110', 'type' => 'Theory'],
            ],
        ],

        // ---- HSC Humanities stream (Class 11-12) ----
        'hsc_hum' => [
            'name'    => 'HSC Humanities (Class 11-12)',
            'classes' => ['11', '12'],
            'subjects' => [
                ['name' => 'History 1st Paper (HSC)',                          'code' => '304', 'type' => 'Theory'],
                ['name' => 'History 2nd Paper (HSC)',                          'code' => '305', 'type' => 'Theory'],
                ['name' => 'Islamic History and Culture 1st Paper (HSC)',      'code' => '267', 'type' => 'Theory'],
                ['name' => 'Islamic History and Culture 2nd Paper (HSC)',      'code' => '268', 'type' => 'Theory'],
                ['name' => 'Civics and Good Governance 1st Paper (HSC)',       'code' => '269', 'type' => 'Theory'],
                ['name' => 'Civics and Good Governance 2nd Paper (HSC)',       'code' => '270', 'type' => 'Theory'],
                ['name' => 'Geography 1st Paper (HSC)',                        'code' => '125', 'type' => 'Theory'],
                ['name' => 'Geography 2nd Paper (HSC)',                        'code' => '126', 'type' => 'Theory'],
                ['name' => 'Economics 1st Paper (HSC)',                        'code' => '109', 'type' => 'Theory'],
                ['name' => 'Economics 2nd Paper (HSC)',                        'code' => '110', 'type' => 'Theory'],
                ['name' => 'Sociology 1st Paper (HSC)',                        'code' => '117', 'type' => 'Theory'],
                ['name' => 'Sociology 2nd Paper (HSC)',                        'code' => '118', 'type' => 'Theory'],
                ['name' => 'Logic 1st Paper (HSC)',                            'code' => '121', 'type' => 'Theory'],
                ['name' => 'Logic 2nd Paper (HSC)',                            'code' => '122', 'type' => 'Theory'],
                ['name' => 'Psychology 1st Paper (HSC)',                       'code' => '123', 'type' => 'Theory'],
                ['name' => 'Psychology 2nd Paper (HSC)',                       'code' => '124', 'type' => 'Theory'],
                ['name' => 'Social Work 1st Paper (HSC)',                      'code' => '271', 'type' => 'Theory'],
                ['name' => 'Social Work 2nd Paper (HSC)',                      'code' => '272', 'type' => 'Theory'],
                ['name' => 'Bengali Literature (HSC)',                         'code' => '273', 'type' => 'Optional'],
                ['name' => 'English Literature (HSC)',                         'code' => '274', 'type' => 'Optional'],
            ],
        ],
    ],
];
