<?php

class App_Service_Assembler_Course
{

    /**
     * @param App_Domain_Course $course
     * @return App_Service_Dto_Course
     */
    public function assembleCourseDto(App_Domain_Course $course)
    {
        $dto = new App_Service_Dto_Course();
        $dto->category = $course->getCategory();
        $dto->title = $course->getTitle();
        $dto->code = $course->getCode();
        $dto->day = $course->getDay();
        $dto->startDate = $course->getStartDate();
        $dto->time = $course->getTime();
        $dto->numWeeks = $course->getNumWeeks();
        $dto->hoursPerWeek = $course->getHoursPerWeek();
        $dto->venue = $course->getVenue();
        $dto->fee = $course->getFee();
        $dto->concession = $course->getConcession();
        $dto->dayTime = $course->getDayTime();
        $dto->description = $course->getDescription();

        return $dto;
    }

}
