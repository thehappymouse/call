<?php

/**
 * Class UnitTest
 */
ini_set('date.timezone','Asia/Shanghai');
class CommonTest extends \UnitTestCase {

    public function testTestCase() {

        $this->assertTrue("2014-05-05" > "2014-05-04");
        $this->assertTrue("2014-05-05" <= "2014-05-05");
        $this->assertTrue("2014-05-05" < "2014-05-07");

    }

    /**
     * DBUtil的单元测试
     */
    public function testDBUtil()
    {
        $s = DataUtil::GetSegmentsByUid(4);

        $this->assertTrue(is_array($s));

        $tid = 1;
        $user = User::find("TeamID = $tid");
        $count = 0;

        foreach($user as $u){
            $arr = DataUtil::GetSegmentsByUid($u->ID);
            $count += count($arr);
        }

        $s2 = DataUtil::GetSegmengsByTid($tid);

        $this->assertTrue(is_array($s2));
        $this->assertTrue(count($s2) == $count);
    }
}
