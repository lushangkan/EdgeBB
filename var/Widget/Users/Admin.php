<?php
if (!defined('__EDGE_ROOT_DIR__')) exit;
/**
 * EdgeBB Forum
 *
 * @copyright  Copyright (c) 2020 Edge team (http://www.mcedge.ink)
 * @license    MIT License
 * @version    $Id$
 */

/**
 * 后台成员列表组件
 *
 * @author qining
 * @category edge
 * @package Widget
 * @copyright Copyright (c) 2020 Edge team (http://www.mcedge.ink)
 * @license MIT License
 */
class Widget_Users_Admin extends Widget_Abstract_Users
{
    /**
     * 分页计算对象
     *
     * @access private
     * @var Edge_Db_Query
     */
    private $_countSql;

    /**
     * 所有文章个数
     *
     * @access private
     * @var integer
     */
    private $_total = false;

    /**
     * 当前页
     *
     * @access private
     * @var integer
     */
    private $_currentPage;

    /**
     * 仅仅输出域名和路径
     *
     * @access protected
     * @return string
     */
    protected function ___domainPath()
    {
        $parts = parse_url($this->url);
        return $parts['host'] . (isset($parts['path']) ? $parts['path'] : NULL);
    }

    /**
     * 发布文章数
     *
     * @access protected
     * @return integer
     */
    protected function ___postsNum()
    {
        return $this->db->fetchObject($this->db->select(array('COUNT(cid)' => 'num'))
                    ->from('table.contents')
                    ->where('table.contents.type = ?', 'post')
                    ->where('table.contents.status = ?', 'publish')
                    ->where('table.contents.authorId = ?', $this->uid))->num;
    }

    /**
     * 执行函数
     *
     * @access public
     * @return void
     */
    public function execute()
    {
        $this->parameter->setDefault('pageSize=20');
        $select = $this->select();
        $this->_currentPage = $this->request->get('page', 1);

        /** 过滤标题 */
        if (NULL != ($keywords = $this->request->keywords)) {
            $select->where('name LIKE ? OR screenName LIKE ?',
            '%' . Edge_Common::filterSearchQuery($keywords) . '%',
            '%' . Edge_Common::filterSearchQuery($keywords) . '%');
        }

        $this->_countSql = clone $select;

        $select->order('table.users.uid', Edge_Db::SORT_ASC)
        ->page($this->_currentPage, $this->parameter->pageSize);

        $this->db->fetchAll($select, array($this, 'push'));
    }

    /**
     * 输出分页
     *
     * @access public
     * @return void
     */
    public function pageNav()
    {
        $query = $this->request->makeUriByRequest('page={page}');;

        /** 使用盒状分页 */
        $nav = new Edge_Widget_Helper_PageNavigator_Box(false === $this->_total ? $this->_total = $this->size($this->_countSql) : $this->_total,
        $this->_currentPage, $this->parameter->pageSize, $query);
        $nav->render('&laquo;', '&raquo;');
    }
}
