<?php

use Phinx\Migration\AbstractMigration;

class CourseHoursPerWeekImportFix extends AbstractMigration
{

    public function up()
    {
        $this->execute(
            "
                ALTER TABLE course
                    MODIFY hours_per_week decimal(3,2) DEFAULT NULL,
                    MODIFY fee decimal(6,2) DEFAULT NULL,
                    MODIFY concession decimal(6,2) DEFAULT NULL
            "
        );
        $this->execute(
            "
                ALTER TABLE digital_gallery_image
                    MODIFY price decimal(6,2) DEFAULT NULL
            "
        );
    }

    public function down()
    {
        $this->execute(
            "
                ALTER TABLE course
                    MODIFY hours_per_week int(11) DEFAULT NULL,
                    MODIFY fee float DEFAULT NULL,
                    MODIFY concession float DEFAULT NULL
            "
        );
        $this->execute(
            "
                ALTER TABLE digital_gallery_image
                    MODIFY price float(6,2) DEFAULT NULL
            "
        );
    }

}
