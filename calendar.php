<?php

class Calendar 
{
	const MONTH_NAMES = [
		'January', 
		'February', 
		"March", 
		"April", 
		"May", 
		"June", 
		"July", 
		"August", 
		"September", 
		"October", 
		"November", 
		"December"
	];

	const WEEK_DAYS_DOUBLE = ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"];

	private $date,
					$day,
					$month,
					$year,
					$calendar;

	public function __construct($year = false)
	{
		$this->date = $year ? strtotime("{$year}-01-01") : time();
		$this->day = date("j", $this->date);
		$this->month = date("n", $this->date);
		$this->year = date("Y", $this->date);
		$this->setCalendar = $this->setCalendar($this->year);
	}

	public function render()
	{
		$calendar = $this->getCalendar($this->year);
		// return print_r($calendar[8]);

		$output = str_pad($this->year, 64, " ", STR_PAD_BOTH) . "\n\n";

		for ($monthRow = 0; $monthRow < 4; $monthRow++) {

			$weekNumber = 0;
			$firstMonthInRow = ($monthRow*3)+1;

			// Add Month Names
			$output .= implode(
				"  ", 
				array_map(function($monthName){
					return str_pad($monthName, 20, " ", STR_PAD_BOTH);
				}, array_slice(self::MONTH_NAMES, $firstMonthInRow-1, 3))
			) . "\n";

			// Add Week Days
			$output .= implode("  ", array_fill(0,3, implode(" ", self::WEEK_DAYS_DOUBLE))) . "\n";

			// Add Days
			do {

				$rowSegments = [];

				for ($currentMonth = $firstMonthInRow; $currentMonth < $firstMonthInRow + 3; $currentMonth++) {

					if (empty($calendar[$currentMonth][$weekNumber])) {
						$rowSegments[] = str_repeat(" ", 20);
						continue;
					}

					$columns = [];

					for ($column = 0; $column < 7; $column++) {
						$columns[] = isset($calendar[$currentMonth][$weekNumber][$column]) ? 
							str_pad($calendar[$currentMonth][$weekNumber][$column], 2, " ", STR_PAD_LEFT) : "  ";	
					}

					$rowSegments[] = implode(" ", $columns);

				}

				$output .= implode("  ", $rowSegments) . "\n";
				$weekNumber++;

			} while ($weekNumber < 6);
			
		}

		return $output;

	}

	private function setCalendar($calendarYear)
	{
		$day = 1;
		$month = 1;
		$year = $calendarYear;

		$date = new DateTime();
		$date->setDate($year, $month, $day);
		$dayOfWeek = $date->format('w');
		$days = array_fill(0, $dayOfWeek, "  ");

		$months = [];
		$weekNumber = 0;

		while ($year === $calendarYear) {
			$year = $date->format('Y');
			$month = intval($date->format('n'));
			$day = intval($date->format('j'));
			$dayOfWeek = intval($date->format('w'));

			if ($day === 1) {
				$weekNumber = 0;
				$months[$month][$weekNumber] = array_fill(0, $dayOfWeek, "  ");
			} elseif ($dayOfWeek === 0) {
				$months[$month][$weekNumber] = [];
			}

			$months[$month][$weekNumber][] = $date->format('j');	
			
			$date->add(new DateInterval('P1D'));

			if ($year !== $date->format('Y')) {
				$month = 1;
				$year++;
			}

			if ($dayOfWeek === 6) {
				$dayOfWeek = 0;
				$weekNumber++;
			} else {
				$dayOfWeek++;
			}

		}

		$this->calendar = $months;
	}

}