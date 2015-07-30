<?php

class AdminCutoffsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /admin/cutoffs
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		$adminCutoff = new AdminCutoff;

		$cutoff['id'] = $adminCutoff->getCutoffbyYearMonth()->id;
		$cutoff['year'] = $adminCutoff->getCutoffbyYearMonth()->year;
		$cutoff['month'] = $adminCutoff->getCutoffbyYearMonth()->month;
		$cutoff['type'] = $adminCutoff->getCutoffbyYearMonth()->cutoff_type;
		$cutoff['dateFrom'][1] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_1;
		$cutoff['dateTo'][1] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_1;
		$cutoff['dateFrom'][2] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_2;
		$cutoff['dateTo'][2] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_2;		

		if ( $cutoff['type'] === 'Monthly' ) {

			$cutOffData = array(
				'cuttoffDateFrom1' => $cutoff['dateFrom'][1],
				'cuttoffDateTo1' => $cutoff['dateTo'][1],
				'cuttoffDateFrom2' => $cutoff['dateFrom'][2],
				'cuttoffDateTo2' => $cutoff['dateTo'][2]
			);

			return $cutOffData;

		} elseif ( $cutoff['type'] === 'Semi Monthly' ) {

			$cutOffData = array(
				'cuttoffDateFrom1' => $cutoff['dateFrom'][1],
				'cuttoffDateTo1' => $cutoff['dateTo'][1],
				'cuttoffDateFrom2' => $cutoff['dateFrom'][2],
				'cuttoffDateTo2' => $cutoff['dateTo'][2]
			);

			return $cutOffData;

		}
			
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /admin/cutoffs/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /admin/cutoffs
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /admin/cutoffs/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /admin/cutoffs/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /admin/cutoffs/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /admin/cutoffs/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}